<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Item;
use DB;


class UploadCSVFileItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $file_path;
    public function __construct($file_path)
    {
       $this->file_path=$file_path; //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       DB::table('test_items')->truncate(); 
       
        $fileContents = file($this->file_path);
    
$items = $this->csvToArray($this->file_path);
    $data = [];
    for ($i = 0; $i < count($items); $i ++)
    {
       
       /*  switch($items[$i]['section']?? "Laboratory"){
            case 'Microbiology' || 'microbiology':
                $section=1;
                break;
            case 'Tuberculosis' || 'tuberculosis':
                $section=2;
                break;

            case 'Laboratory' || 'laboratory':
                $section=3;
                break;
            case 'Diagnostics' || 'diagnostics':
                $section=4;
                break;
            case 'Blood Science' || 'blood Science':
                $section=5;
                break;
            case 'Parasitology' || 'parasitology':
                $section=6;
                break;
        }  */

        $data[] = [
            'code' => $items[$i]['code']?? 1,
            'name' =>$items[$i]['name'],
            'description' =>$items[$i]['description'],

             ];
       /*  $data[] = [
            'code' => $items[$i]['code']?? 1,
            'laboratory_id' =>$items[$i]['Lab_id'] ?? 1,
            'laboratory_sections_id' => $section,
            'item_name' => $items[$i]['Generic name']?? 'Missing',
            'item_description' => $items[$i]['Description'],
            'minimum_level' => $items[$i]['minimum level'] ?? 8,
            'maximum_level' => $items[$i]['maximum level'] ?? 8,
            'unit_issue' => $items[$i]['Unit of Issue'] ?? "box",
            'status' => 'active',
           
           // .. so..on..and..on
        ]; */
        //User::firstOrCreate($customerArr[$i]);
    }
    //dd($data);

    DB::table('test_items')->insert($data);
    
   /*  foreach ($fileContents as $line) {
        $data = str_getcsv($line);

        Item::create([
            'name' => $data[0],
            'price' => $data[1],
            // Add more fields as needed
        ]);
    } */
    }
   protected function csvToArray($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }

    return $data;
}
}
