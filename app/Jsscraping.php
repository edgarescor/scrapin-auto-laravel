use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class JavaScriptScraper
{
    protected $driver;

    public function __construct()
    {
        $capabilities = DesiredCapabilities::chrome();
        $options = new ChromeOptions();
        $options->addArguments([
            '--headless', // Ejecutar en modo sin cabeza (headless)
            '--disable-gpu',
        ]);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $driver = RemoteWebDriver::create(env('SELENIUM_DRIVER_URL'), $capabilities);
        $this->driver = $driver;
    }

    public function scrapeWithJavaScript($url)
    {
        $this->driver->get($url);

        // Realiza acciones adicionales utilizando JavaScript en la página cargada
        // Por ejemplo, hacer clic en un botón o extraer datos actualizados

        // Obtén el contenido HTML de la página resultante
        $html = $this->driver->getPageSource();

        // Realiza el procesamiento adicional necesario

        // Cierra el navegador
        $this->driver->quit();

        return $html;
    }
}