<?php

namespace App\Service;

use App\Entity\Status;
use App\Entity\Text;

class ComponentValidation
{
    /**
     * Comprueba si es una imagen de formato válido.
     *
     * @param   string $image
     *
     * @return  bool
     */
    public function checkValidImage($image)
    {
        $ext = strtolower($this->getExtension($image));

        switch ($ext) {
            case 'jpg || jpeg':
            case 'png':
                return true;
                break;
            default:
                return false;
        }
    }

    /**
     * Comprueba si es un vídeo de formato válido.
     * @param   string $file
     *
     * @return  bool
     */
    public function checkValidVideo($file)
    {
        $ext = strtolower($this->getExtension($file));

        switch ($ext) {
            case 'mp4':
            case 'webm':
                return true;
                break;
            default:
                return false;
        }
    }

    /**
     * Comprueba si es un texto tiene las condiciones válidas.
     *
     * @param   string $text
     *
     * @return  bool
     */
    public function checkValidText($text)
    {
        $length = strlen($text);

        return ($length <= Text::MAX_CHAR);
    }

    /**
     * Comprueba si el componente está en estado publicable.
     *
     * @param   string $status
     *
     * @return  bool
     */
    public function checkValidStatus($status) {
        return ($status === Status::READY);
    }

    /**
     * Devuelve la extensión de un nombre de fichero.
     *
     * @param   $file
     *
     * @return  bool|mixed
     */
    private function getExtension($file)
    {
        $extension = end(explode(".", $file));

        return $extension ? $extension : false;
    }
}
