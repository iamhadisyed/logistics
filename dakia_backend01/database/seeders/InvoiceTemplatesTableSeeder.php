<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InvoiceTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('invoice_templates')->delete();
        
        \DB::table('invoice_templates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Default Template',
                'image' => 'default_template.png',
                'invoice_function' => 'SavePDFFile',
                'summary_invoice_function' => 'SaveSummaryInvoicePDFFile',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'OWE Template',
                'image' => 'owe_template.png',
                'invoice_function' => 'oweInvoiceTemplate',
                'summary_invoice_function' => 'oweSummaryInvoiceTemplate',
            ),
            2 => 
            array (
                'id' => 1,
                'name' => 'Default Template',
                'image' => 'default_template.png',
                'invoice_function' => 'SavePDFFile',
                'summary_invoice_function' => 'SaveSummaryInvoicePDFFile',
            ),
            3 => 
            array (
                'id' => 2,
                'name' => 'OWE Template',
                'image' => 'owe_template.png',
                'invoice_function' => 'oweInvoiceTemplate',
                'summary_invoice_function' => 'oweSummaryInvoiceTemplate',
            ),
            4 => 
            array (
                'id' => 1,
                'name' => 'Default Template',
                'image' => 'default_template.png',
                'invoice_function' => 'SavePDFFile',
                'summary_invoice_function' => 'SaveSummaryInvoicePDFFile',
            ),
            5 => 
            array (
                'id' => 2,
                'name' => 'OWE Template',
                'image' => 'owe_template.png',
                'invoice_function' => 'oweInvoiceTemplate',
                'summary_invoice_function' => 'oweSummaryInvoiceTemplate',
            ),
            6 => 
            array (
                'id' => 1,
                'name' => 'Default Template',
                'image' => 'default_template.png',
                'invoice_function' => 'SavePDFFile',
                'summary_invoice_function' => 'SaveSummaryInvoicePDFFile',
            ),
            7 => 
            array (
                'id' => 2,
                'name' => 'OWE Template',
                'image' => 'owe_template.png',
                'invoice_function' => 'oweInvoiceTemplate',
                'summary_invoice_function' => 'oweSummaryInvoiceTemplate',
            ),
            8 => 
            array (
                'id' => 1,
                'name' => 'Default Template',
                'image' => 'default_template.png',
                'invoice_function' => 'SavePDFFile',
                'summary_invoice_function' => 'SaveSummaryInvoicePDFFile',
            ),
            9 => 
            array (
                'id' => 2,
                'name' => 'OWE Template',
                'image' => 'owe_template.png',
                'invoice_function' => 'oweInvoiceTemplate',
                'summary_invoice_function' => 'oweSummaryInvoiceTemplate',
            ),
        ));
        
        
    }
}