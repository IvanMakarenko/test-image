<?php

namespace app\services;

use ErrorException;
use Yii;
use yii\web\UploadedFile;

use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use Intervention\Image\ImageManager;

class AddPreviewByColor
{
    const TEXT = 'test';
    const FONT_FILE = '@runtime/watermarks/Catalish Huntera.ttf';
    const FONT_SIZE = 48;

    const FILE_TYPE_GIF = 1;
    const FILE_TYPE_JPG = 2;
    const FILE_TYPE_PNG = 3;
    const FILE_TYPE_BMP = 6;

    const COLOR_RATE = 1.1;

    const ENABLE_TYPES = [
        'image/jpeg',
        'image/png',
    ];

    const COLOR_MASK = [
        'red'   => '#000000', // red   => black
        'blue'  => '#ffff00', // blue  => yellow
        'green' => '#ff0000', // green => red
    ];

    protected $file;

    private $isDebug = 0;

    public function __construct(UploadedFile $model)
    {
        $this->file = $model;

        if (!in_array($this->file->type, self::ENABLE_TYPES)) {
            throw new ErrorException('File is not an image.');
        }
    }

    public function execute()
    {
        $palette = Palette::fromFilename($this->file->tempName);
        return $this->renderWithWatermark($this->getMaskColor($palette));
    }

    protected function getMaskColor(Palette $palette)
    {
        $name  = 'blue';
        $debug = null;

        foreach ($palette->getMostUsedColors(3) as $color) {
            $rgb = Color::fromIntToRGB($color);
            // Normolize RGB for division
            $rgb['r'] = $rgb['r'] ?: 1;
            $rgb['g'] = $rgb['g'] ?: 1;
            $rgb['b'] = $rgb['b'] ?: 1;

            if (($rgb['r'] / $rgb['g']) > self::COLOR_RATE && ($rgb['r'] / $rgb['b']) > self::COLOR_RATE) {
                $name = 'red';
            }
            if (($rgb['g'] / $rgb['r']) > self::COLOR_RATE && ($rgb['g'] / $rgb['b']) > self::COLOR_RATE) {
                $name = 'green';
            }
            if (($rgb['b'] / $rgb['r']) > self::COLOR_RATE && ($rgb['b'] / $rgb['g']) > self::COLOR_RATE) {
                $name = 'blue';
            }

            $debug .= "<tr>"
                ."<td style='background-color:".Color::fromIntToHex($color).";width:2em;'>&nbsp;</td>"
                ."<td>".($rgb['r'].'; '.$rgb['g'].'; '. $rgb['b'])." - ".$name."</td>"
                ."<td style='background: ".(self::COLOR_MASK[$name])."'>".(self::COLOR_MASK[$name])."</td>"
                ."</tr>\n";
        }

        if ($this->isDebug) {
            die('<table>'.$debug.'</table>');
        }

        return self::COLOR_MASK[$name];
    }

    protected function renderWithWatermark($color)
    {
        // 1. Open image
        $manager = new ImageManager(['driver' => 'imagick']);
        // 2. Resize image
        $img = $manager->make($this->file->tempName);
        // 3. Lowly brightness
        $img->brightness(-35);

        // 4. Prepare text
        $title = wordwrap(self::TEXT, 20, PHP_EOL);
        $x = ($img->width() - self::FONT_SIZE) / 2;
        $y = ($img->height() - self::FONT_SIZE) / 2;

        // 5. Write text
        $img->text($title, $x, $y, function($font) {
            $font->file(Yii::getAlias(self::FONT_FILE));
            $font->size(48);
            $font->color($color);
            $font->align('left');
            $font->valign('center');
        });

        header('Content-type: image/png');
        echo $img->response();
        Yii::$app->end();
    }
}