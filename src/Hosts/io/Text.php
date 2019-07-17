<?php
namespace Puleeno\Goader\Hosts\io;

use GuzzleHttp\Client;
use Puleeno\Goader\Abstracts\Host;
use Puleeno\Goader\Command;
use Puleeno\Goader\Environment;
use Puleeno\Goader\Hook;
use Puleeno\Goader\Logger;

class Text extends Host
{
    public function formatLink($originalLink)
    {
        $command = Command::getCommand();
        if ($command['host']) {
            $supportedHosts = Environment::supportedHosters();
            $host = new $supportedHosts[$command['host']]($originalLink, []);
            return $host->formatLink($originalLink);
        }
        $originalLink = Hook::apply_filters('text_link', $originalLink);
        $prefix = $command['url'] ? $command['url'] : '';

        return sprintf('%1$s%2$s', $prefix, $originalLink);
    }

    public function download($directoryName = null)
    {
        $images = explode("\n", file_get_contents($this->host['path']));
        if (!empty($images)) {
            $httpClient = new Client();
            foreach ($images as $index => $image) {
                $image = $this->formatLink(trim($image));
                if (!$this->validateLink($image)) {
                    Logger::log(sprintf('The url #%d is invalid with value "%s"', $index + 1, $image));
                    continue;
                }

                Environment::setCurrentIndex($index + 1);

                $fileName = $this->generateFileName($image, false);
                $this->getContent($image, $httpClient)->saveFile($fileName);
            }
        }
    }
}