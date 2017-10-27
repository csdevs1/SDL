<?php
    use \Illuminate\Support\Facades\DB;
    class VehiclesList{
        public function __contruct()
        {
        }
        public function PlateNumbers()
        {
            $plates=DB::table('vehicle')->select(['plate_number'])->get();
            return $plates;
        }
        
        public function VehicleList($plates,$date)
        {
            $vehicle_group = array ();

            foreach ( $plates as $plate)
            {
                //Log::info('Creando nodo para ['.$plate.']');
                $documents=$this->Document_by_Plate_by_Date( $plate,$date );
                if(isset($documents) && !empty($documents)){
                    $vehicle_group[]= array(
                        'plate_number'  =>  $plate,
                        'driver'        =>  $this->Driver_by_Plate_Number   ( $plate ),
                        'documents'     =>  $this->Document_by_Plate_by_Date( $plate,$date ),
                        'location'      =>  $this->Location_by_Plate        ( $plate )
                    );
                }
            }
            //\Log::info($vehicle_group);
            //dd($vehicle_group);
            return $vehicle_group;
        }

        private function Location_by_Plate (  $plate_number )
        {
            $Location = array () ;

            if (  empty( $plate_number ))
            {
                Log::info("Plate Number [".$plate_number."] no contiene data.");
                return $Location ;
            }

            $location = DB::table('device')
                        ->WHERE('plate_number',$plate_number)
                        ->first();

            if( empty($location))
            {
               // Log::info('Plate  ['.$plate_number.'] sin informacion.');
                return $Location ;
            }

            return array(
                'longitude'     =>  $location->mobile_longitude,
                'latitude'      =>  $location->mobile_latitude,
                'battery'       =>  $location->mobile_battery,
                'precision'     =>  $location->mobile_precision,
                'updated'       =>  $location->mobile_updated,
            );
        }

        private function Driver_by_Plate_Number ( $plate_number )
        {
            $Driver = array () ;

            if (  empty( $plate_number ))
            {
                Log::info("Plate Number [".$plate_number."] no contiene data.");
                return $Driver ;
            }

            $id_employee = DB::table('vehicle')
                        ->WHERE('plate_number',$plate_number)
                        ->pluck('id_employee');

            // Ahora se determina los datos del chofer a partir del id del
            // vehiculo
            $employee_data = DB::table('employee')
                            ->WHERE('id',$id_employee)
                            ->first();

            if( empty($employee_data))
            {
                Log::info('Employee id ['.$id_employee.'] no registrado.');
                return $Driver ;
            }

            return array(
                'name'  =>  $employee_data->name,
                'phone' =>  $employee_data->mobile
            );
        }

        private function Document_by_Plate_by_Date( $plate, $date )
        {
            $document_node = array();

            if( empty ($plate) )
            {
                Log::info('Patente vacia o nula ['.$plate.']');
                return $document_node ;
            }

            $documents = DB::table('document')
                        ->WHERE('plate_number',$plate)
                        ->WHERE('delivery_date',$date)
                        ->get();

           if( empty ($documents) )
            {
                Log::info('No hay documentos asociados a la patente ['.$plate.'] fecha ['.$date.']');
                return $document_node ;
            }

            foreach (  $documents as $document )
            {
            $customer = DB::table('customer')
                ->WHERE('id', $document->id_client )
                ->first();

                $document_node[] = array (
                    //'status'            =>  $document->id_status,
                    'status'            =>  $this->Status($document->id_status)['status'],
                    'dispatch_quantity' =>  $document->dispatch_quantity,
                    'dispatch_quantity' =>  $document->dispatch_quantity,
                    'dispatch_bulk'     =>  $document->dispatch_bulk,
                    'dispatch_volume'   =>  $document->dispatch_volume,
                    'rejected_quantity' =>  $document->rejected_quantity,
                    'rejected_bulk'     =>  $document->rejected_bulk,
                    'rejected_volume'   =>  $document->rejected_volume,
                    'id_customer'       =>  $document->id_client,
                    'modified'          =>  $document->modified,
                    'delivery_type'     =>  $document->delivery_type,
                    'containera_out'	=>  $document->containera_out,
                    'containerb_out'	=>  $document->containerb_out,
                    'containera_ret'	=>  $document->containera_ret,
                    'containerb_ret'	=>  $document->containerb_ret,
                    'latitude'          =>  $customer->latitude,
                    'longitude'         =>  $customer->longitude,
                );
            }
            return $document_node;
        }

        private function Status ( $status_id )
        {

            $status = array ( );

            if (  empty ($status_id)  )
            {
                Log::info('Status ['.$status_id.'] no definido.');

                return $status ;
            }


            $status = DB::table('status')
                    ->WHERE('id',$status_id)
                    ->first();

            return array(
                'status'        =>  $status->status,
                'label'         =>  $status->label,
                'description'   =>  $status->description
            );
        }

        public function DriversList(){
            $drivers=DB::table('employee')->select(['name','id'])->get();
            return $drivers;
        }
    }
