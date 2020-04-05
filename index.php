<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

error_reporting(E_ALL & ~E_NOTICE);

require __DIR__ . '/vendor/autoload.php';

function siteURL()
{
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol . $domainName;
}

define('SITE_URL', siteURL());

$templateName = 'default';
$templatePath = __DIR__ . '/templates/' . $templateName;

$loader = new \Twig\Loader\FilesystemLoader($templatePath);
$twig = new \Twig\Environment($loader, ['cache' => __DIR__ . '/cache', 'debug' => true,]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

$router = new \Bramus\Router\Router();

$router->get('/', function () use ($twig, $templateName) {


    echo $twig->render('index.twig',
        [
            'base_url' => SITE_URL,
            'template_name' => $templateName
        ]
    );

});

$router->post('/upload', function () use ($twig, $templateName) {

    $ds = DIRECTORY_SEPARATOR;  //1
    $storeFolder = 'uploads';   //2

    if (!empty($_FILES)) {

        $tempFile = $_FILES['file']['tmp_name'];          //3

        $targetPath = dirname(__FILE__) . '/' . $storeFolder . '/';  //4

        $targetFile = $targetPath . $_FILES['file']['name'];  //5

        move_uploaded_file($tempFile, $targetFile); //6

    }

    return json_encode(SITE_URL . '/uploads/' . $_FILES['file']['name']) ;
});

$router->run();