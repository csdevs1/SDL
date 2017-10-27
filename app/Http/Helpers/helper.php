<?php
    use \Illuminate\Support\Facades\DB;
    class ListVehicles{
        public function __contruct()
        {
        }
        public function ListComplete($type,$id_group_of_office,$date,$id_g_office=0)
        {
            if ( empty( $type ) || ( $type != 'full' && $type != 'low' ))
            {
                return response()->json(['Parametro de entrada incorrecto.', 1001]);
            }

            if ( empty( $id_group_of_office )) {
                return '';
            }


           // Salta a la nueva funcion para procesamiento full
            if ( $type == 'full' )
            {
                $result=$this->full_new( $id_group_of_office , $date,$id_g_office );
                $json=json_encode($result,true);
                return $result;

            }else{
                $result=$this->low_new( $id_group_of_office );
                $json=json_encode($result,true);
                return $result;
            }

            $group_of_office = DB::table('group_of_office')
                                ->where('id', $id_group_of_office )
                                ->first();

            if ( empty( $group_of_office )) {
                return response()->json(['Grupo de oficina no existe.', 1001]);
            }


            $offices_arr= array();

            // Se determinan las oficianas relacioandas a ese grupo de
            //  oficinas.
            $office_group_offices = DB::table('office_group_office')
                        ->where('id_group_of_office',$id_group_of_office)
                        ->get();

            if ( empty($office_group_offices) )
            {
                // El grupo no tiene oficinas asignadas.
                return $this->response(['result' => [] ]);
            }


            foreach ( $office_group_offices as $office_group_office ) // <- Aca tenemos las oficinas.
            {
                //Log::info("office group [".$id_group_of_office."] office Id [".$office_group_office->id_office."]" );

                // Determinar los datos relacionados a esa officina
                $office=  DB::table('office')
                        ->where('id',$office_group_office->id_office)
                        ->first();

                if( empty($office))
                {
                    continue;
                }

                // Determinar los grupos de vehiculos relacionados con la
                // oficina
                $office_group_vehicles = DB::table('office_group_vehicle')
                        ->where('id_office',$office->id)
                        ->get();

                $vehicle_groups = array();

                if( empty($office_group_vehicles))
                {
                    //$vehicle_groups[] = array (
                    //        'id'       => $vehicle_group_db->id,
                    //        'name'     => $vehicle_group_db->label,
                    //        'vehicles' => $vehicle
                    //        );
                    continue;
                }


                foreach ( $office_group_vehicles as $office_group_vehicle )
                {
                    #Log::info("office  [".$office->id."] Vehicle Group [".$office_group_vehicle->id_group_of_vehicle."]" );

                    if ( $type == 'low')
                    {
                        $vehicle_groups[] = $this->Plate_by_Vehicle_Group( $office_group_vehicle->id_group_of_vehicle );
                    }else{
                        $vehicle_groups[] = $this->Vehicle_by_Vehicle_Group($office_group_vehicle->id_group_of_vehicle , $date);
                    }


                }
                $offices_arr[] = array(
                    'office_id'    =>  $office->id,
                    'office_name'  =>  $office->label,
                    'vehicle_groups'    => $vehicle_groups
                );
            }
            return response()->json($offices_arr);
        }
        public function getLastId(){
            $last_office_id = DB::table('office')
                ->select(['id'])
                ->orderBy('id', 'DESC' )
                ->first();
            $id=DB::select('SELECT * FROM office_group_office ogo JOIN office o ON ogo.id_office=o.id ORDER BY o.id DESC;');
            if(isset($id) && !empty($id))
                $id_office=$id[0]->id_group_of_office;
            else
                $id_office='';
            return $id_office;
        }
        
        private function full_new( $id_group_of_office , $date,$id_g_office)
        {
            $q="";
            if($id_g_office>0)
                $q='AND vg.id_group_of_vehicle = '.$id_g_office; // Used to 
            $rows = DB::select('SELECT array_to_json(array_agg(row_to_json(row_all)))  as  results
                FROM
            (SELECT o.id, o.label as name ,
                    (SELECT array_to_json(array_agg(row_to_json(row)))
                    FROM
                        (SELECT gov.id, gov.label as name,
                            (SELECT array_to_json(array_agg(row_to_json(vehicles_row)))
                            FROM ( SELECT v.id, v.plate_number as name, array_to_json(array_agg(d.document)) as documents
                                FROM vehicle_group vg
                                    JOIN vehicle v
                                    ON vg.id_vehicle = v.id
                                    LEFT JOIN document d
                                    ON  ( d.plate_number = v.plate_number and  d.delivery_date = \''.$date.'\')
                                    WHERE ogv.id_group_of_vehicle = vg.id_group_of_vehicle '.$q.'
                                    GROUP BY v.id, v.plate_number) vehicles_row) as vehicles
                    FROM office_group_vehicle ogv
                    JOIN group_of_vehicle gov
                    ON ogv.id_group_of_vehicle = gov.id
                    WHERE ogv.id_office = o.id) row ) as vehicle_groups
            from group_of_office goo
                JOIN office_group_office  ogo
                    ON ogo.id_group_of_office = goo.id
                JOIN office o
                    ON o.id = ogo.id_office
            where goo.id = '. $id_group_of_office .' GROUP BY o.id, o.label) row_all;');

            foreach ( $rows as $row )
            {
                $data_row = array();
                foreach ( $row as $key => $value )
                {
                    $data_row [ $key] = $value ;

                    return json_decode($value);
                }

            }

        }

        private function low_new( $id_group_of_office )
        {

            $rows = DB::select('SELECT array_to_json(array_agg(row_to_json(row_all)))  as  results
                FROM
            (select o.id, o.label as name,
            (SELECT array_to_json(array_agg(row_to_json(row)))
                FROM
                    (SELECT
                     gov.id, gov.label as name ,
                            (SELECT array_to_json(array_agg(v.plate_number))
                            FROM vehicle_group vg
                                            JOIN vehicle v
                                                    ON vg.id_vehicle = v.id
                            WHERE ogv.id_group_of_vehicle = vg.id_group_of_vehicle) as vehicles
                    FROM office_group_vehicle ogv
                            JOIN group_of_vehicle gov
                                    ON ogv.id_group_of_vehicle = gov.id
                    WHERE ogv.id_office = o.id) row ) as vehicle_groups
            from group_of_office goo
            JOIN office_group_office  ogo
                    ON ogo.id_group_of_office = goo.id
                JOIN office o
                    ON o.id = ogo.id_office
            where goo.id = '.$id_group_of_office.'
            GROUP BY o.id, o.label ) row_all;');

            foreach ( $rows as $row )
            {
                $data_row = array();
                foreach ( $row as $key => $value )
                {
                    $data_row [ $key] = $value ;

                    return json_decode($value);
                }

            }

        }
    }
