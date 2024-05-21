<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OptionType;
use App\Models\Assignment;
use App\Models\Status;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        OptionType::create([
            'name' => 'Radio',
            'type' => 'radio',
            'tag' => 'input',
            'multiple_select' => true
        ]);

        OptionType::create([
            'name' => 'Checkboxes',
            'type' => 'checkbox',
            'tag' => 'input',
            'multiple_select' => true
        ]);

        OptionType::create([
            'name' => 'Short Text',
            'type' => 'text',
            'tag' => 'text',
            'multiple_select' => false
        ]);

        OptionType::create([
            'name' => 'Paragraph',
            'type' => 'textarea',
            'tag' => 'text',
            'multiple_select' => false
        ]);

        OptionType::create([
            'name' => 'Dropdown',
            'type' => 'select',
            'tag' => 'select',
            'multiple_select' => false
        ]);

        Status::create([
            'name' => 'Created',
        ]);

        Status::create([
            'name' => 'Publish',
        ]);

        Status::create([
            'name' => 'Draft',
        ]);

        Status::create([
            'name' => 'Locked',
        ]);

        Status::create([
            'name' => 'Expired',
        ]);

        Assignment::create([
            'name' => 'Department',
            'model' => 'App\Models\Department',
            'employee_function_name'=>'departments',
            'reference_column'=> 'department_id'
        ]);
        Assignment::create([
            'name' => 'Company',
            'model' => 'App\Models\Company',
            'employee_function_name'=>'companies',
            'reference_column'=> 'company_id'
        ]);

        Assignment::create([
            'name' => 'Cluster',
            'model' => 'App\Models\Cluster',
            'employee_function_name'=>'clusters',
            'reference_column'=> 'cluster_id'
        ]);

        Assignment::create([
            'name' => 'Employee',
            'model' => 'App\Models\Employee',
            'employee_function_name'=> null,
            'reference_column'=> 'id'
        ]);

        Assignment::create([
            'name' => 'Location',
            'model' => 'App\Models\Location',
            'employee_function_name'=>'locations',
            'reference_column'=> 'location_id'
        ]);
    }
}
