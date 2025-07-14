<?php

namespace Tests;

use HeadlessChromium\BrowserFactory;
use PDO;
use PHPUnit\Framework\TestCase;

class AppTests extends TestCase
{
    private $pdo;
    private $browser;
    private $appPageHost;
    private $appDbHost;
    private $appDbUser;
    private $appDbName;
    private $appDbPass;

    protected function setUp(): void
    {
        // Declare environment variables at the beginning
        $this->appPageHost = getenv('APP_PAGE_HOST');
        $this->appDbHost = getenv('APP_DB_HOST');
        $this->appDbUser = getenv('APP_DB_USER');
        $this->appDbName = getenv('APP_DB_NAME');
        $this->appDbPass = getenv('APP_DB_PASS');

        // Set up PDO connection
        $dsn = "mysql:host={$this->appDbHost};dbname={$this->appDbName}";
        $this->pdo = new PDO($dsn, $this->appDbUser, $this->appDbPass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Set up headless Chrome browser
        $browserFactory = new BrowserFactory('chromium-browser');
        $this->browser = $browserFactory->createBrowser([
            'headless' => true,
            'noSandbox' => true, // Required for Docker environments
        ]);
    }

    public function testWebPageMatchesDatabase()
    {
        // Fetch data from the web page
        $page = $this->browser->createPage();
        $page->navigate($this->appPageHost)->waitForNavigation();
        $html = $page->getHtml();

        // Parse HTML table
        $dom = new \DOMDocument();
        @$dom->loadHTML($html); // Suppress warnings for malformed HTML
        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//table//tr');

        $webData = [];
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue; // Skip header row
            }
            $cells = $xpath->query('td', $row);
            $rowData = [];
            foreach ($cells as $cell) {
                $rowData[] = $cell->nodeValue;
            }
            $webData[] = $rowData;
        }

        // Fetch data directly from the database
        $stmt = $this->pdo->query('SELECT * FROM test');
        $dbData = $stmt->fetchAll(PDO::FETCH_NUM); // Fetch as numeric array to match web output

        // Compare web data with database data
        $this->assertEquals($dbData, $webData, 'Web page data does not match database data.');
    }

    protected function tearDown(): void
    {
        // Close browser
        if ($this->browser) {
            $this->browser->close();
        }
        // Close PDO connection
        $this->pdo = null;
    }
}
