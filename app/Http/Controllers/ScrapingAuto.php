<?php

namespace App\Http\Controllers;

use App\Models\MaatPersonaVehiculos;
use App\Models\VehiculosPRT;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Goutte\Client;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverExpectedCondition;

class JavaScriptScraper
{

    protected $driver;

    public function __construct()
    {
        set_time_limit(0);
        $capabilities = DesiredCapabilities::chrome();
        $options = new ChromeOptions();
        $options->addArguments([
            
            '--ignore-certificate-errors', // Ejecutar en modo sin cabeza (headless)
            '--disable-gpu',
            '--disable-features=RendererCodeIntegrity','--disable-features=VizDisplayCompositor',
        ]);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $serverUrl = 'http://localhost:9515';
        $driver = RemoteWebDriver::create($serverUrl, $capabilities);
        $this->driver = $driver;
    }

    /**
 * Limpiar y separar rut
 */
function limpiarRut(String $rut, String $opcion)
{
    // Rut a retornar
    $reemplazo = $rut;

    // Verificar opción
    switch($opcion)
    {
        case 'limpio':
            $reemplazo = str_replace(array('.','-'), array('',''), $rut);
            break;

        case 'dv':
            $reemplazo = explode('-', $rut)[1];
            break;

        case 'limpio-dv':
            $reemplazo = substr($rut,'-1');
            break;

        case 'no-dv':
            $reemplazo = str_replace(array('.'), array(''), substr($rut,0,-1));
            break;

        case 'separado':
            $reemplazo = explode('-', str_replace(array('.'), array(''), $rut));
            break;
    }

    return $reemplazo;
}

    public function scrapeWithJavaScript($url,$patente)
    {
        set_time_limit(0);
        $this->driver->get($url);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('patentechile')
        );
        // Realiza acciones adicionales utilizando JavaScript en la página cargada
        // Por ejemplo, hacer clic en un botón o extraer datos actualizados

        // Obtén el contenido HTML de la página resultante
        $html = $this->driver->getPageSource();

        // Realiza el procesamiento adicional necesario

        sleep(3);
        $input = $this->driver->findElement(WebDriverBy::id('txtTerm'));
        $input->sendKeys($patente);

        $valor = $input->getAttribute('value');

        $Boton = $this->driver->findElement(WebDriverBy::id('btnConsultar'));

        sleep(3);
        $Boton->click();

//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 git remote add origin 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@github.com:edgarescor/scrapin-auto-laravel.git
        sleep(8);
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('resultados')
        );


        $tabla = $this->driver->findElements(WebDriverBy::id('tblDataVehicle'));

if (count($tabla) > 0) {
   
            // Buscar la tabla por su atributo id
            $tabla = $this->driver->findElement(WebDriverBy::id('tblDataVehicle'));
            // Obtener todas las filas (tr) de la tabla
            $filas = $tabla->findElements(WebDriverBy::tagName('tr'));
    
            $i=1;
            $patente_completa="";
            $rut="";
            $dv="";
            $propietario="";
            $color="";
            $multa="";
            $procedencia="";
            $fabricante="";
            $sinestrado="";
            foreach ($filas as $fila) {
                // Obtener todas las celdas (td) de la fila actual
                $celdas = $fila->findElements(WebDriverBy::tagName('td'));
            
                // Recorrer las celdas
    
                $array_posiciones=array(6,2,3,11,14,20,21,22);
                if(in_array($i,$array_posiciones)){
    
                    $ii=1;
                    foreach ($celdas as $celda) {
                        // Obtener el texto de la celda
                       if($ii==2){
    
                        switch ($i) {
                            case 6:
                                # code...
                                    $patente_completa = $celda->getText();
                                break;
                            case 2:
                                # code...
                                    $rut = self::limpiarRut(self::limpiarRut($celda->getText(),'limpio'),'no-dv');
                                    $dv  = self::limpiarRut(self::limpiarRut($celda->getText(),'limpio'),'limpio-dv');
                                break;
                            case 3:
                                # code...
                                $propietario = $celda->getText();
                                break;
                            case 11:
                                # code...
                                $color = $celda->getText();
                                break;
                            case 14:
                                # code...
                                $multa = $celda->getText();
    
                                   
                                break;
                            case 20:
                                # code...
                                $procedencia = $celda->getText();
                                break;
                            case 21:
                                # code...
                                $fabricante = $celda->getText();
                                break;
                            case 22:
                                # code...
                                $sinestrado = $celda->getText();
                                break;
                            
                        }
                       
                       }
                       $ii++;
                                          
                        // Realizar acciones con el contenido de la celda
                        // ...
                    }
    
                }
                
    
                $i++;
            }
            
            
    
            //aqui el inserte a la tabla vehiculos
            $patente_registrada = MaatPersonaVehiculos::create([
                'patente'=>$patente,
                'patente_completa'=>$patente_completa,
                'rut'=>$rut,
                'dv'=>$dv,
                'propietario'=>$propietario,
                'color'=>$color,
                'multa'=>$multa,
                'procedencia'=>$procedencia,
                'fabricante'=>$fabricante,
                'sinestrado'=>$sinestrado,
                'fecha_carga'=>Carbon::now()->format("Y-m-d h:i:s")
            ]);
            //dd($filas);
            // Cierra el navegador
} else {
    // El elemento no existe en la página
    echo "El elemento no existe.";
}
        


        $this->driver->quit();

        return $html;
    }
}

class ScrapingAuto extends Controller
{
    //

    public function ConectarTupatente(Client $client)
    {

  set_time_limit(0);

       
        $url = 'https://www.patentechile.com';
 

        $buscar_behiculos = VehiculosPRT::where('revisado',0)->limit(100)->get();

        foreach ($buscar_behiculos as $value) {
            # code...
            $scraper = new JavaScriptScraper();
            $html = $scraper->scrapeWithJavaScript($url,$value->patente);
            $actualizar_id= VehiculosPRT::where('patente',$value->patente);
            $actualizar_id->update([
                'revisado'=>1
            ]);
            echo "--->".$value->patente."<br>";

            
        }
        
        //$html = $scraper->scrapeWithJavaScript($url,'RSZH89');

        return $html; 
//echo "termine con éxito";
        //return $html; 
      
    }
}
