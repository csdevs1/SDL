<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Document;
use Auth;
use DB;

class Upload extends Controller
{
    private function save($table,$values,$condition_val=null){
        \Log::info($table);
        \Log::info($values);
        try{
            $id='';
            if(isset($condition_val) && !empty($condition_val))
                $id=DB::table($table)->select(['id'])->where($condition_val[0],$condition_val[1])->first();
            if ($id==null || $id==''){ //If document doesn't exist, it saves whatever it's been sent
                if($table=='office_group_vehicle' || $table=='vehicle_group')
                    $insertion=DB::table($table)->insert($values);
                else{
                    $insertion=DB::table($table)->insertGetId($values);
                }
                return array('id'=>$insertion,'exist'=>false); // Saves the values and returns the record's id
            }else
                return array('id'=>$id->id,'exist'=>true);
        }catch(\Exception $e){
            return false;
        }
    }
    
    private function update_document($id){
        try{
            $quantities=DB::select('SELECT document_item.id_document,SUM(dispatch_quantity) as dispatch_quantity, SUM(dispatch_bulk) as dispatch_bulk, SUM(dispatch_volume) as dispatch_volume FROM product JOIN document_item ON document_item.id_product=product.id WHERE document_item.id_document='.$id.' GROUP BY document_item.id_document;');
            $array = json_decode(json_encode($quantities[0]), true);
            $values_to_update=array(
                'dispatch_volume'=>$array['dispatch_volume'],
                'dispatch_bulk'=>$array['dispatch_bulk'],
                'dispatch_quantity'=>$array['dispatch_quantity']);
            return DB::table('document')
                ->where('id',$id)
                ->update($values_to_update);
        }catch(\Exception $e){
            return false;
        }
    }
    
    public function importFile(Request $request)
	{
        $file=$request->file('upload_file');
		if(isset($file) && !empty($file)){
            //All this code below move it to a function called getCSV to get its values, and create another one for XLS, depending on the extension, call the proper function
            $csv = array_map('utf8_encode', file($file->getRealPath()));
            $csv_array=array();
			$path = $file->getRealPath();
            $name=$file->getClientOriginalName();

            foreach ($csv as $key => $value) {            
                $value=explode(';', $value);
                array_push($csv_array,$value);
            }

            $document_to_insert=array();
            $items=array();
            $doc_id=array();
            $document_products=array();
            $errors=array();
            $sum_dispatch_quantity = 0;
            $sum_dispatch_bulk     = 0;
            $sum_dispatch_volume   = 0;
            $fila=1;
            if(file_exists('../public/docs/'.$name))
                array_push($errors,'Archivo ya existe');
            foreach($csv_array as $key=>$val){ // Check values
                foreach($val as $k=>$v)
                    if(empty($val[$k])) array_push($errors,'Valor en la columna '.$csv_array[0][$k].', fila: '.$fila.' esta vacio');
                /*if(empty($val[2])) array_push($errors,'Valor en la columna '.$csv_array[0][2].', fila: '.$fila.' esta vacio');
                if(empty($val[3])) array_push($errors,'Valor en la columna '.$csv_array[0][3].', fila: '.$fila.' esta vacio');
                if(empty($val[6])) array_push($errors,'Valor en la columna '.$csv_array[0][6].', fila: '.$fila.' esta vacio');
                if(empty($val[7])) array_push($errors,'Valor en la columna '.$csv_array[0][7].', fila: '.$fila.' esta vacio');
                if(empty($val[9])) array_push($errors,'Valor en la columna '.$csv_array[0][9].', fila: '.$fila.' esta vacio');
                if(empty($val[10])) array_push($errors,'Valor en la columna '.$csv_array[0][11].', fila: '.$fila.' esta vacio');
                if(empty($val[11])) array_push($errors,'Valor en la columna '.$csv_array[0][10].', fila: '.$fila.' esta vacio');
                if(empty($val[11])) array_push($errors,'Valor en la columna '.$csv_array[0][10].', fila: '.$fila.' esta vacio');
                if(empty($val[14])) array_push($errors,'Valor en la columna '.$csv_array[0][14].', fila: '.$fila.' esta vacio');
                if(empty($val[15])) array_push($errors,'Valor en la columna '.$csv_array[0][15].', fila: '.$fila.' esta vacio');
                if(empty($val[16])) array_push($errors,'Valor en la columna '.$csv_array[0][16].', fila: '.$fila.' esta vacio');
                if(empty($val[17])) array_push($errors,'Valor en la columna '.$csv_array[0][17].', fila: '.$fila.' esta vacio');
                if(empty($val[18])) array_push($errors,'Valor en la columna '.$csv_array[0][18].', fila: '.$fila.' esta vacio');*/
                $fila++;
            }
            if(empty($errors)){
                //Number of ITEMS
                $n_docs=array();
                $n_employees=array();
                $n_offices=array();
                $n_vehicles=array();
                $n_customers=array();
                $n_products=array();
                //Number of ITEMS

                $new_employees=0;
                $new_office=0;
                $new_vehicle=0;
                $new_customer=0;
                $new_documents=0;
                $new_products=0;
                foreach($csv_array as $key=>$val){
                    //\Log::info($val);
                    //dd($val);
                    if($key!=0){
                        list($day,$month,$year) = explode("-",$val[5]);
                        $id_client =  DB::table('customer')
                            ->select(['id'])
                            ->WHERE('code',$val[14])
                            ->WHERE('subcode',$val[15])
                            ->first();
                        $id_product=DB::table('product')->select(['id'])->WHERE('code',$val[20])->first();

                        //Array for document
                        $tmp=array(
                            'route_sheet'=>$val[0],
                            'delivery_type'=>$val[2],
                            'order_number'=>$val[3],
                            //'uploaded_date'=>date("Y-m-d"),
                            'delivery_date'=>"{$day}-{$month}-{$year}",
                            'channel'=>$val[6],
                            'payment'=>$val[7],
                            'document'=>$val[12],
                            'plate_number'=>$val[9],
                            //'subcode'=>$val[15],
                            'location'=>'',
                            'id_status'=>1,
                            'rejected_quantity'=>0,
                            'rejected_bulk'=>0,
                            'rejected_volume'=>0,
                            'id_client'=>$id_client->id,
                            'enable'=>'t',
                            'created'=>date('Y-m-d H:i:s'),
                            'modified'=>NULL
                        );

                        // PRODUCT'S ARRAY
                        $tmp_item=array(
                            "code"=>$val[20],
                            "description"=>$val[21],
                            "unit"=>"",
                            "enable"=>"t",
                            "created"=>date('Y-m-d H:i:s'),
                            "modified"=>NULL
                        );

                        array_push($document_to_insert,$tmp);
                        array_push($items,$tmp_item);

                        // SAVE DOCUMENTS
                        $comparisson=array('document',$val[12]);
                        $d_id=$this->save('document',$tmp,$comparisson); // Save current Document ID in a var to be inserted in document_item
                        //$d_id['exist']==true?$existent_documents++:$new_documents++;
                        $n_docs[$val[12]]=1;
                        $d_id['exist']==true?\Log::info('Document Exist'):$new_documents++;
                        array_push($doc_id,$d_id['id']); //Save all document IDs into an array to be updated later

                        // SAVE PRODUCTS
                        $comparisson=array('code',$val[20]);
                        $n_products[$val[20]]=1;
                        $product_id=$this->save('product',$tmp_item,$comparisson);
                        $product_id['exist']==true?\Log::info('Product Exists'):$new_products++;

                        // DOCUMENTITEM ARRAY
                        $doc_item_arr=array(
                            'id_document'=>$d_id['id'],
                            'id_product'=>$product_id['id'],
                            'delivery_date'=>"{$day}-{$month}-{$year}",
                            'id_status'=>2,
                            "dispatch_quantity"=>$val[22],
                            "dispatch_bulk"=>$val[23],
                            "dispatch_volume"=>str_replace(",", ".", $val[24]),
                            'rejected_quantity'=>0,
                            'rejected_bulk'=>0,
                            'rejected_volume'=>0,
                        );

                        // SAVE DOCUMENTITEM
                        $comparisson=array('id_document',$d_id['id']);
                        $document_item=$this->save('document_item',$doc_item_arr,$comparisson);
                        // Employee
                        $employee=array(
                            'name'=>$val[11],
                            'mobile'=>0,
                            'code'=>$val[10],
                            'type_employee'=>0,
                            'boss_id'=>0,
                            'enable'=>true
                        );
                        $n_employees[$val[10]]=1;

                        $comparisson=array('code',$val[10]);
                        $emp=$this->save('employee',$employee,$comparisson);

                        $emp['exist']==true?\Log::info('Document Exist'):$new_employees++;
                        // Employee

                        // Office
                            $office=array(
                                'label'=>$val[19],
                                'description'=>$val[19],
                                'enable'=>true
                            );
                            $n_offices[$val[19]]=1;

                            $comparisson=array('label',$val[19]);
                            $off=$this->save('office',$office,$comparisson);

                            $group_of_vehicle=$this->save('group_of_vehicle',$office,$comparisson);

                            $ogv=array(
                                'id_group_of_vehicle'=>$group_of_vehicle['id'],
                                'id_office'=>$off['id'],
                            );
                            $office_group_vehicle=$this->save('office_group_vehicle',$ogv);

                            $off['exist']==true?\Log::info('Office Exist'):$new_office++;
                        // Office

                        // Vehicle
                            $vehicle=array(
                                'plate_number'=>$val[9],
                                'label'=>$val[9],
                                'id_employee'=>$emp['id'],
                                'id_device'=>0,
                                'enable'=>true
                            );

                            $comparisson=array('plate_number',$val[9]);
                            $veh=$this->save('vehicle',$vehicle,$comparisson);
                            $n_vehicles[$val[9]]=1;
                            $veh['exist']==true?\Log::info('Vehicle Exist'):$new_vehicle++;

                            $vg=array(
                                'id_group_of_vehicle'=>$group_of_vehicle['id'],
                                'id_vehicle'=>$veh['id'],
                            );
                            $vehicle_group=$this->save('vehicle_group',$vg);
                        // Vehicle

                        // Customer
                            $customer=array(
                                'name'=>$val[16],
                                'code'=>$val[14],
                                'address'=>$val[17],
                                'subcode'=>$val[15],
                                'id_office'=>$off['id'],
                                'comune'=>$val[18],
                                'phone'=>0,
                                'enable'=>true
                            );
                            $n_customers[$val[14]]=1;
                            $comparisson=array('code',$val[14]);
                            $cus=$this->save('customer',$vehicle,$comparisson);

                            $cus['exist']==true?\Log::info('Customer Exists'):$new_customer++;
                        // Customer
                    }
                }
                $existent_documents=count($n_docs)-$new_documents;
                $existent_employees=count($n_employees)-$new_employees;
                $existent_office=count($n_offices)-$new_office;
                $existent_vehicle=count($n_vehicles)-$new_vehicle;
                $existent_customer=count($n_customers)-$new_customer;
                $existent_products=count($n_products)-$new_products;
                $save_file=array(
                    'name'=>$name,
                    'id_user'=>Auth::user()->id,
                    'lines'=>count($csv_array),
                    'employee_new'=>$new_employees,
                    'employee_duplicated'=>$existent_employees,
                    'employee_error'=>0,
                    'office_new'=>$new_office,
                    'office_duplicated'=>$existent_office,
                    'office_error'=>0,
                    'vehicle_new'=>$new_vehicle,
                    'vehicle_duplicated'=>$existent_vehicle,
                    'vehicle_error'=>0,
                    'customer_new'=>$new_customer,
                    'customer_duplicated'=>$existent_customer,
                    'customer_error'=>0,
                    'document_new'=>$new_documents,
                    'document_duplicated'=>$existent_documents,
                    'document_error'=>0,
                    'product_new'=>$new_products,
                    'product_duplicated'=>$existent_products,
                    'product_error'=>0,
                    'enable'=>true,
                    'created'=>date('Y-m-d H:i:s'),
                );
                $comparisson=array('name',$name);
                $upload=$this->save('upload',$save_file,$comparisson);
                \Log::info('Number of existent documents: '.$existent_documents);
                //dd($upload);
                foreach($doc_id as $key=>$val){
                    \Log::info('Updated: '.$this->update_document($val));
                }
                $file->move('../public/docs', $name);
            }
            return response()->json(['errors'=>$errors]);
        }
	}
}
