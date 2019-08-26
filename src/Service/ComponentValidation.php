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
    public static function checkValidImage($image)
    {
        $ext = strtolower(self::getExtension($image));

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
    public static function checkValidVideo($file)
    {
        $ext = strtolower(self::getExtension($file));

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
    public static function checkValidText($text)
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
    public static function checkValidStatus($status) {
        return ($status === Status::READY);
    }

    /**
     * Devuelve la extensión de un nombre de fichero.
     *
     * @param   $file
     *
     * @return  bool|mixed
     */
    public static function getExtension($file)
    {
        $extension = end(explode(".", $file));

        return $extension ? $extension : false;
    }
}
