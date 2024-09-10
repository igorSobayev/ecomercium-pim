<?php 
namespace App\Console\Commands; 
use Illuminate\Console\Command; 
use DB;

class testdbconnection extends Command{ 
   /** 
   * The name and signature of the console command. 
   * @var string 
   */ 
   protected $signature = 'testdbconnection';

   /**
   * The console command description. 
   * @var string 
   */ 
   protected $description = 'Testing DB connection';

   /** 
   * Create a new command instance. 
   * 
   * @return void 
   */ 
   public function __construct() { 
      parent::__construct(); 
   }

   /** 
   * Execute the console command. 
   * 
   * @return int 
   */ public function handle() { 
      try { 
         DB::connection()->getPDO(); 
         dump('Database is connected. Database Name is : ' . DB::connection()->getDatabaseName()); 
      } catch (Exception $e) { 
         dump('Database connection failed'); 
      } 
   } 
}