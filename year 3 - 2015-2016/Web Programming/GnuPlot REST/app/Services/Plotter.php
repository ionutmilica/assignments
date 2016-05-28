<?php

namespace App\Services;

use App\Models\Plot;
use App\Models\Template;

class Plotter
{

    /**
     * Generate plot images
     *
     * @param Plot $plot
     * @param Template $template
     * @param $dataInput
     */
    public function generate(Plot $plot, Template $template, $dataInput)
    {
        $dataFile = sprintf('data_%s.dat', $plot->id);
        $imageFile = sprintf('%s.png', $plot->id);
        $scriptFile = sprintf('script_%s.gp', $plot->id);

        $default = "set term png\nset output \"".$imageFile."\"\n";

        $file = storage_path('app/'.$scriptFile);
        $data = storage_path('app/'.$dataFile);
        $image = storage_path('app/'.$imageFile);

        file_put_contents($file, $default.str_replace('##data-file##', $dataFile, $template->script));
        file_put_contents($data, file_get_contents($dataInput));

        shell_exec('cd ../storage/app && gnuplot '.$scriptFile);
        unlink($file);
        unlink($data);

        $newPath = '/uploads/'.$imageFile;
        rename($image, base_path('public'.$newPath));

        $plot->image = $newPath;
        $plot->save();
    }

    /**
     * Create a preview for a template
     *
     * @param Template $template
     * @param $dataInput
     * @return string
     */
    public function preview(Template $template, $dataInput)
    {
        $session = uniqid($template->id.'_'.mt_rand(90000, 99999));
        $dataFile = sprintf('data_%s.dat', $session);
        $imageFile = sprintf('output_%s.png', $session);
        $scriptFile = sprintf('script_%s.gp', $session);

        $default = "set term png\nset output \"".$imageFile."\"\n";

        $file = storage_path('app/'.$scriptFile);
        $data = storage_path('app/'.$dataFile);
        $image = storage_path('app/'.$imageFile);

        file_put_contents($file, $default.str_replace('##data-file##', $dataFile, $template->script));
        file_put_contents($data, file_get_contents($dataInput));

        shell_exec('cd ../storage/app && gnuplot '.$scriptFile);

        $imageData = 'data:image/png;base64,'.base64_encode(file_get_contents($image));

        unlink($file);
        unlink($data);
        unlink($image);

        return $imageData;
    }
}