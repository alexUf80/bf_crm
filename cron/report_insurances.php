<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__).'/../');

require 'autoload.php';

require 'PHPExcel/Classes/PHPExcel.php';

class ReportInsurancesCron extends Core
{
    private $excel;
    
    private $filename = 'report_insurances.xls';
    
    public function __construct()
    {
    	parent::__construct();

        $this->excel = new PHPExcel();
        
        $this->filename = $this->config->root_dir.'files/reports/'.$this->filename;
        
        
        $data = $this->get_data();
        if (!empty($data))
        {
            $this->create_file($data);
    
            $this->send_report();
        }
    }
    
    private function send_report()
    {
        $to = 'kolgotin_vi@akticom.ru';
        $to = 'alpex-s@rambler.ru';
        $subject = 'Реестр страховок "Наличное+" от'.date('d.m.Y');
        $message = '<h1>Реестр страховок "Наличное+" от'.date('d.m.Y').'</h1>';
        $from = $this->settings->notify_from_email;
        $reply = '';
        $filenames = array(
            $this->filename
        );
        
        $this->notify->email($to, $subject, $message, $from, $reply, $filenames);

        echo $message;
    }
    
    private function create_file($data)
    {
        $style_bold = array(
            'font' => array(
                'name' => 'Calibri',
                'size'=>13,
                'bold'=>true
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       	=> true,
            )
        );
        $style_header = array(
            'font' => array(
                'name' => 'Calibri',
                'size'=>13,
                'bold'=>true
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       	=> true,
            )
        );
    
        $left_italic = array(
            'font' => array(
                'name' => 'Calibri',
                'italic' => true
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       	=> true,
            )
        );
        
        $style_ten = array(
            'font' => array(
                'name' => 'Calibri',
                'size' => 10,
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       	=> true,
            )
        );
        
        $style_border_medium = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '111111')
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '111111')
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '111111')
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '111111')
                )
            )
        );

        $style_border_thin = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '111111')
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '111111')
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '111111')
                )
            )
        );


        $this->excel->setActiveSheetIndex(0);
        $active_sheet = $this->excel->getActiveSheet();
        
        $active_sheet->setTitle("Реестр от ".date('d.m.Y'));
    
        $this->excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
        $this->excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
        $active_sheet->getRowDimension(1)->setRowHeight(75);
        $active_sheet->getRowDimension(2)->setRowHeight(40);
        $active_sheet->getStyle('A1:O2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A1:O2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $active_sheet->getColumnDimension('A')->setWidth(30);
        $active_sheet->getColumnDimension('B')->setWidth(30);
        $active_sheet->getColumnDimension('C')->setWidth(15);
        $active_sheet->getColumnDimension('D')->setWidth(10);
        $active_sheet->getColumnDimension('E')->setWidth(10);
        $active_sheet->getColumnDimension('F')->setWidth(10);
        $active_sheet->getColumnDimension('G')->setWidth(15);
        $active_sheet->getColumnDimension('H')->setWidth(15);
        $active_sheet->getColumnDimension('I')->setWidth(10);
        $active_sheet->getColumnDimension('J')->setWidth(15);
        $active_sheet->getColumnDimension('K')->setWidth(20);
        $active_sheet->getColumnDimension('L')->setWidth(10);
        $active_sheet->getColumnDimension('M')->setWidth(10);
        $active_sheet->getColumnDimension('N')->setWidth(10);
        $active_sheet->getColumnDimension('O')->setWidth(10);
        
        $active_sheet->getPageMargins()->setTop(0.5);
        $active_sheet->getPageMargins()->setBottom(0.5);
    	
        
        $active_sheet->mergeCells("A1:A2");
        $active_sheet->setCellValue('A1', 'Номер сертификата/номер кредитного договора');
        $active_sheet->getStyle('A1:A2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('A1:A2')->getAlignment()->setWrapText(true);
                
        $active_sheet->mergeCells("B1:B2");
        $active_sheet->setCellValue('B1', 'Название продукта');
        $active_sheet->getStyle('B1:B2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('A1:A2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("C1:C2");
        $active_sheet->setCellValue('C1', 'Дата продажи сертификата');
        $active_sheet->getStyle('C1:C2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('A1:A2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("D1:F1");
        $active_sheet->setCellValue('D1', 'Застрахованный/Выгодоприобретатель');
        $active_sheet->getStyle('D1:D1')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('A1:A2')->getAlignment()->setWrapText(true);

        $active_sheet->setCellValue('D2', 'Фамилия');
        $active_sheet->getStyle('D2:D2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('A1:A2')->getAlignment()->setWrapText(true);

        $active_sheet->setCellValue('E2', 'Имя');
        $active_sheet->getStyle('E2:E2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('E2:E2')->getAlignment()->setWrapText(true);

        $active_sheet->setCellValue('F2', 'Отчество');
        $active_sheet->getStyle('F2:F2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('F2:F2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("G1:G2");
        $active_sheet->setCellValue('G1', 'Телефон');
        $active_sheet->getStyle('G1:G2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('G1:G2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("H1:H2");
        $active_sheet->setCellValue('H1', 'Дата рождения');
        $active_sheet->getStyle('H1:H2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('H1:H2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("I1:I2");
        $active_sheet->setCellValue('I1', 'Пол');
        $active_sheet->getStyle('I1:I2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('I1:I2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("J1:J2");
        $active_sheet->setCellValue('J1', 'Паспортные данные');
        $active_sheet->getStyle('J1:J2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('J1:J2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("K1:K2");
        $active_sheet->setCellValue('K1', 'Адрес застрахованного имущества');
        $active_sheet->getStyle('K1:K2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('K1:K2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("L1:L2");
        $active_sheet->setCellValue('L1', 'Дата начала ответственности');
        $active_sheet->getStyle('L1:L2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('L1:L2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("M1:M2");
        $active_sheet->setCellValue('M1', 'Дата окончания ответственности');
        $active_sheet->getStyle('M1:M2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('M1:M2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("N1:N2");
        $active_sheet->setCellValue('N1', 'Страховая сумма, руб.');
        $active_sheet->getStyle('N1:N2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('N1:N2')->getAlignment()->setWrapText(true);

        $active_sheet->mergeCells("O1:O2");
        $active_sheet->setCellValue('O1', 'Страховая премия, руб.');
        $active_sheet->getStyle('O1:O2')->applyFromArray($style_border_medium);
        $active_sheet->getStyle('O1:O2')->getAlignment()->setWrapText(true);

        $current_row = 3;
        foreach ($data as $item)
        {
            $active_sheet->setCellValue('A'.$current_row, $item->number);
            $active_sheet->setCellValue('B'.$current_row, $item->product_name);
            $active_sheet->setCellValue('C'.$current_row, $item->sale_date);
            $active_sheet->setCellValue('D'.$current_row, $item->lastname);
            $active_sheet->setCellValue('E'.$current_row, $item->firstname);
            $active_sheet->setCellValue('F'.$current_row, $item->patronymic);
            $active_sheet->setCellValue('G'.$current_row, $item->phone);
            $active_sheet->setCellValue('H'.$current_row, $item->birthdate);
            $active_sheet->setCellValue('I'.$current_row, $item->gender);
            $active_sheet->setCellValue('J'.$current_row, $item->passport_data);
            $active_sheet->setCellValue('K'.$current_row, $item->address);
            $active_sheet->setCellValue('L'.$current_row, $item->start_date);
            $active_sheet->setCellValue('M'.$current_row, $item->end_date);
            $active_sheet->setCellValue('N'.$current_row, $item->amount);
            $active_sheet->setCellValue('O'.$current_row, $item->premium);

            $active_sheet->getStyle('A'.$current_row.':A'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('B'.$current_row.':B'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('C'.$current_row.':C'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('D'.$current_row.':D'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('E'.$current_row.':E'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('F'.$current_row.':F'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('G'.$current_row.':G'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('H'.$current_row.':H'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('I'.$current_row.':I'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('J'.$current_row.':J'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('K'.$current_row.':K'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('L'.$current_row.':L'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('M'.$current_row.':M'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('N'.$current_row.':N'.$current_row)->applyFromArray($style_border_thin);
            $active_sheet->getStyle('O'.$current_row.':O'.$current_row)->applyFromArray($style_border_thin);
        
            $current_row++;
        }



        $objWriter = PHPExcel_IOFactory::createWriter($this->excel,'Excel5');


        $objWriter->save($this->filename);

    }
    

    private function get_data()
    {
        $data = array();
        
    	if ($insurances = $this->insurances->get_insurances(array('sent' => 0)))
        {
            $user_ids = array();
            $users = array();
            foreach ($insurances as $insurance)
                $user_ids[] = $insurance->user_id;
            foreach ($this->users->get_users(array('id' => $user_ids)) as $user)
                $users[$user->id] = $user;
            
            foreach ($insurances as $insurance)
            {
                $item = new StdClass();
                
                $item->number = $insurance->number;
                $item->product_name = 'Страхование от НС';
                $item->sale_date = date('d.m.Y', strtotime($insurance->create_date));
                $item->lastname = $users[$insurance->user_id]->lastname;
                $item->firstname = $users[$insurance->user_id]->firstname;
                $item->patronymic = $users[$insurance->user_id]->patronymic;
                $item->phone = $users[$insurance->user_id]->phone_mobile;
                $item->birthdate = $users[$insurance->user_id]->birth;
                $item->gender = $users[$insurance->user_id]->gender == 'male' ? 'Мужской' : 'Женский';
                $item->passport_data = $users[$insurance->user_id]->passport_serial.' от '.$users[$insurance->user_id]->passport_date;
                $item->address = '';
                $item->start_date = date('d.m.Y', strtotime($insurance->start_date));
                $item->end_date = date('d.m.Y', strtotime($insurance->end_date));
                $item->amount = $insurance->amount / 0.15;
                $item->premium = $insurance->amount; 
                
                $data[] = $item;
                
                $this->insurances->update_insurance($insurance->id, array('sent_status' => 1, 'send_date' => date('Y-m-d H:i:s')));
            }
        }
        
        return $data;
    }
    
    
    
}
new ReportInsurancesCron();