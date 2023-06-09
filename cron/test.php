<?php

error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('memory_limit', '1024M');

chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class test extends Core
{

    public function __construct()
    {
        parent::__construct();
        /*$classname = "Fns_scoring";
        $scoring_result = $this->{$classname}->run_scoring(111708);*/
        //42223,42635,42222,42231,41978,41991
        $string = '42697
42698
42702
42704
42707
42711
42716
42727
42729
42734
42735
42736
42737
42743
42744
42746
42747
42749
42753
42754
42756
42758
42763
42764
42765
42771
42772
42776
42783
42790
42791
42793
42794
42796
42797
42798
42802
42804
42807
42810
42812
42818
42819
42820
42821
42823
42826
42830
42832
42833
42836
42837
42841
42843
42844
42850
42853
42854
42855
42856
42859
42861
42862
42864
42866
42867
42868
42869
42870
42873
42876
42880
42881
42882
42883
42885
42888
42890
42893
42894
42898
42908
42914
42919
42922
42923
42926
42928
42933
42934
42939
42940
42943
42950
42952
42953
42954
42955
42956
42957
42958
42959
42960
42964
42968
42969
42979
42983
42984
42986
42989
42990
42991
42993
42995
42997
42999
43002
43004
43006
43008
43017
43018
43022
43023
43024
43029
43036
43037
43039
43040
43045
43048
43050
43051
43055
43056
43057
43062
43063
43064
43066
43067
43068
43069
43070
43071
43072
43074
43075
43076
43078
43081
43085
43088
43089
43091
43093
43095
43097
43100
43101
43102
43105
43110
43111
43112
43116
43117
43119
43120
43121
43122
43123
43125
43130
43131
43134
43136
43141
43143
43152
43154
43155
43156
43158
43160
43161
43162
43163
43164
43165
43166
43167
43168
43170
43171
43174
43178
43180
43181
43182
43185
43186
43188
43190
43191
43196
43197
43198
43201
43206
43207
43208
43210
43211
43212
43213
43215
43216
43218
43219
43220
43222
43223
43224
43227
43228
43229
43230
43231
43232
43233
43235
43237
43238
43241
43243
43244
43245
43246
43248
43249
43250
43252
43253
43254
43255
43257
43258
43259
43264
43266
43271
43273
43278
43279
43280
43281
43282
43284
43285
43286
43287
43288
43289
43291
43292
43295
43296
43298
43300
43302
43303
43305
43312
43319
43322
43325
43326
43329
43332
43333
43334
43335
43336
43338
43341
43342
43344
43345
43346
43347
43351
43352
43353
43356
43357
43359
43360
43362
43363
43368
43372
43374
43375
43380
43381
43382
43384
43385
43387
43389
43390
43392
43394
43395
43397
43400
43401
43402
43405
43406
43408
43409
43410
43417
43423
43424
43427
43429
43432
43434
43438
43466
43469
43473
43476
43478
43484
43488
43500
43502
43503
43509
43513
43526
43527
43529
43535
43542
43544
43546
43549
43558
43560
43566
43567
43588
43590
43591
43596
43597
43601
43624
43626
43627
43633
43635
43636
43646
43647
43650
43655
43658
43662
43664
43669
43673
43675
43676
43678
43682
43686
43688
43689
43693
43694
43697
43698
43700
43706
43708
43709
43712
43713
43715
43717
43721
43727
43732
43735
43737
43739
43740
43741
43742
43743
43745
43749
43750
43755
43756
43758
43759
43764
43765
43766
43769
43771
43778
43779
43780
43785
43786
43791
43792
43793
43798
43799
43802
43803
43805
43807
43809
43811
43812
43813
43814
43815
43817
43818
43819
43821
43825
43830
43833
43837
43838
43842
43843
43854
43856
43859
43860
43861
43863
43865
43866
43871
43873
43875
43876
43878
43879
43885
43886
43890
43892
43893
43896
43897
43898
43901
43904
43906
43908
43910
43911
43912
43917
43918
43919
43922
43923
43925
43927
43928
43929
43930
43931
43934
43936
43938
43940
43941
43946
43947
43952
43953
43956
43963
43964
43967
43972
43973
43975
43977
43980
43982
43987
43989
43991
43992
43993
43994
43997
43998
43999
44000
44010
44011
44015
44016
44018
44019
44021
44026
44027
44028
44029
44030
44034
44036
44038
44040
44042
44043
44044
44045
44046
44048
44049
44051
44052
44053
44054
44056
44058
44061
44063
44065
44067
44068
44070
44072
44073
44074
44075
44077
44079
44080
44083
44084
44085
44087
44090
44094
44095
44096
44098
44100
44104
44105
44107
44113
44115
44116
44122
44125
44129
44130
44131
44135
44139
44143
44147
44148
44154
44155
44156
44157
44158
44159
44162
44163
44166
44169
44170
44172
44174
44175
44176
44178
44179
44181
44182
44183
44186
44187
44189
44191
44192
44195
44196
44198
44199
44201
44202
44205
44207
44208
44210
44211
44213
44220
44221
44222
44223
44225
44229
44235
44236
44237
44238
44240
44241
44242
44243
44244
44247
44251
44253
44255
44258
44259
44260
44263
44264
44268
44269
44271
44277
44278
44279
44282
44283
44284
44288
44290
44291
44292
44295
44296
44298
44302
44304
44306
44311
44313
44314
44315
44316
44317
44319
44323
44324
44328
44331
44332
44334
44336
44343
44344
44346
44351
44352
44355
44359
44362
44363
44369
44370
44374
44375
44376
44377
44378
44383
44386
44393
44396
44397
44399
44400
44402
44403
44404
44405
44407
44408
44410
44411
44414
44416
44418
44419
44420
44421
44423
44428
44429
44430
44431
44435
44437
44442
44443
44448
44450
44453
44454
44455
44457
44458
44459
44460
44462
44464
44468
44469
44472
44475
44477
44478
44482
44486
44487
44491
44492
44493
44496
44497
44500
44503
44504
44505
44507
44508
44511
44512
44513
44517
44519
44520
44521
44525
44527
44529
44531
44532
44534
44535
44536
44541
44542
44545
44548
44552
44553
44554
44557
44559
44561
44563
44564
44565
44566
44567
44572
44573
44575
44577
44578
44579
44581
44582
44583
44585
44586
44587
44589
44590
44591
44593
44594
44597
44600
44602
44604
44608
44610
44611
44615
44620
44622
44623
44624
44625
44626
44628
44632
44634
44637
44640
44642
44644
44646
44648
44649
44651
44652
44654
44656
44660
44662
44668
44669
44670
44672
44673
44674
44683
44685
44689
44693
44694
44697
44700
44701
44703
44704
44705
44706
44707
44708
44709
44710
44711
44712
44713
44714
44715
44716
44717
44718
44720
44723
44724
44725
44727
44729
44730
44733
44736
44738
44744
44748
44749
44750
44753
44754
44758
44760
44761
44762
44763
44766
44767
44768
44769
44770
44774
44776
44777
44778
44779
44781
44783
44784
44786
44788
44794
44796
44798
44801
44804
44805
44806
44808
44811
44815
44818
44819
44821
44826
44829
44830
44834
44835
44836
44837
44839
44840
44841
44842
44845
44846
44847
44849
44850
44851
44853
44854
44855
44858
44860
44863
44866
44869
44870
44872
44874
44879
44880
44882
44883
44891
44899
44909
44910
44912
44921
44923
44924
44925
44928
44933
44939
44940
44941
44942
44944
44946
44948
44949
44951
44952
44954
44956
44958
44961
44964
44965
44967
44968
44969
44970
44971
44973
44983
44986
44988
44989
44992
44993
44995
44997
44998
45001
45002
45004
45005
45009
45010
45012
45014
45016
45018
45021
45022
45023
45029
45030
45031
45033
45034
45035
45038
45040
45043
45044
45046
45048
45050
45051
45053
45056
45057
45060
45061';
        $orderIds = explode(PHP_EOL, $string);
        foreach ($orderIds as $id) {
            $order = OrdersORM::where('id', '=', $id)->first();
            if ($order) {
                echo $order->click_hash.PHP_EOL;
            } else {
                echo "-".PHP_EOL;
            }
        }
        //$classname = "Nbkiscore_scoring";

        //$scoring_result = $this->{$classname}->run_scoring(123710);
    }

    public function create_document($document_type, $contract)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period . ' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);

        $insurance_cost = $this->insurances->get_insurance_cost($contract_order);

        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => $insurance_cost,
        );

        $params['user'] = $this->users->get_user($contract->user_id);
        $params['order'] = $this->orders->get_order($contract->order_id);
        $params['contract'] = $contract;


        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => json_encode($params),
        ));

    }

    private function import_addresses()
    {
        $tmp_name = $this->config->root_dir . '/files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $outer_id = $active_sheet->getCell('P' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $Regindex = $active_sheet->getCell('I' . $row)->getValue();
            $Regregion = $active_sheet->getCell('R' . $row)->getValue();
            $Regcity = $active_sheet->getCell('S' . $row)->getValue();
            $Regstreet = $active_sheet->getCell('T' . $row)->getValue();
            $Regbuilding = $active_sheet->getCell('U' . $row)->getValue();
            $Regroom = $active_sheet->getCell('X' . $row)->getValue();

            $Faktindex = $active_sheet->getCell('J' . $row)->getValue();
            $Faktregion = $active_sheet->getCell('Z' . $row)->getValue();
            $Faktcity = $active_sheet->getCell('AA' . $row)->getValue();
            $Faktstreet = $active_sheet->getCell('AB' . $row)->getValue();
            $Faktbuilding = $active_sheet->getCell('AC' . $row)->getValue();
            $Faktroom = $active_sheet->getCell('AF' . $row)->getValue();

            $regaddress = "$Regindex $Regregion $Regcity $Regstreet $Regbuilding $Regroom";
            $faktaddress = "$Faktindex $Faktregion $Faktcity $Faktstreet $Faktbuilding $Faktroom";

            $faktaddres = [];
            $faktaddres['adressfull'] = $faktaddress;
            $faktaddres['zip'] = $Faktindex;
            $faktaddres['region'] = $Faktregion;
            $faktaddres['city'] = $Faktcity;
            $faktaddres['street'] = $Faktstreet;
            $faktaddres['building'] = $Faktbuilding;
            $faktaddres['room'] = $Faktroom;

            $regaddres = [];
            $regaddres['adressfull'] = $regaddress;
            $regaddres['zip'] = $Regindex;
            $regaddres['region'] = $Regregion;
            $regaddres['city'] = $Regcity;
            $regaddres['street'] = $Regstreet;
            $regaddres['building'] = $Regbuilding;
            $regaddres['room'] = $Regroom;

            foreach ($regaddres as $key => $address) {
                if ($address == '#NULL!')
                    unset($regaddres[$key]);
            }

            foreach ($faktaddres as $key => $address) {
                if ($address == '#NULL!')
                    unset($faktaddres[$key]);
            }

            $this->db->query("
            SELECT *
            from s_users
            where outer_id = ?
            ", $outer_id);

            $user = $this->db->result();


            $this->Addresses->update_address($user->regaddress_id, $regaddres);
            $this->Addresses->update_address($user->faktaddress_id, $faktaddres);

        }
    }

    private function import_clients()
    {
        $tmp_name = $this->config->root_dir . '/files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $created = $active_sheet->getCell('AQ' . $row)->getFormattedValue();
            $birth = $active_sheet->getCell('D' . $row)->getFormattedValue();
            $passport_date = $active_sheet->getCell('AK' . $row)->getFormattedValue();

            $outer_id = $active_sheet->getCell('P' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $Regindex = $active_sheet->getCell('I' . $row)->getValue();
            $Regregion = $active_sheet->getCell('R' . $row)->getValue();
            $Regcity = $active_sheet->getCell('S' . $row)->getValue();
            $Regstreet = $active_sheet->getCell('T' . $row)->getValue();
            $Regbuilding = $active_sheet->getCell('U' . $row)->getValue();
            $Regroom = $active_sheet->getCell('X' . $row)->getValue();

            $Faktindex = $active_sheet->getCell('J' . $row)->getValue();
            $Faktregion = $active_sheet->getCell('Z' . $row)->getValue();
            $Faktcity = $active_sheet->getCell('AA' . $row)->getValue();
            $Faktstreet = $active_sheet->getCell('AB' . $row)->getValue();
            $Faktbuilding = $active_sheet->getCell('AC' . $row)->getValue();
            $Faktroom = $active_sheet->getCell('AF' . $row)->getValue();

            $regaddress = "$Regindex $Regregion $Regcity $Regstreet $Regbuilding $Regroom";
            $faktaddress = "$Faktindex $Faktregion $Faktcity $Faktstreet $Faktbuilding $Faktroom";

            $reg_id = $this->Addresses->add_address(['adressfull' => $regaddress]);
            $fakt_id = $this->Addresses->add_address(['adressfull' => $faktaddress]);

            $fio = explode(' ', $active_sheet->getCell('A' . $row)->getValue());

            $phone = preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('K' . $row)->getValue());
            $phone = str_split($phone);
            $phone[0] = '7';
            $phone = implode('', $phone);

            $user = [
                'firstname' => ucfirst($fio[1]),
                'lastname' => ucfirst($fio[0]),
                'patronymic' => ucfirst($fio[2]),
                'outer_id' => $outer_id,
                'phone_mobile' => $phone,
                'email' => $active_sheet->getCell('AG' . $row)->getValue(),
                'gender' => $active_sheet->getCell('AN' . $row)->getValue() == 'Мужской' ? 'male' : 'female',
                'birth' => date('d.m.Y', strtotime($birth)),
                'birth_place' => $active_sheet->getCell('G' . $row)->getValue(),
                'passport_serial' => $active_sheet->getCell('AH' . $row)->getValue() . '-' . $active_sheet->getCell('AI' . $row)->getValue(),
                'passport_date' => date('d.m.Y', strtotime($passport_date)),
                'passport_issued' => $active_sheet->getCell('AJ' . $row)->getValue(),
                'subdivision_code' => $active_sheet->getCell('H' . $row)->getValue(),
                'snils' => $active_sheet->getCell('AM' . $row)->getValue(),
                'inn' => $active_sheet->getCell('AL' . $row)->getValue(),
                'workplace' => $active_sheet->getCell('L' . $row)->getValue(),
                'workaddress' => $active_sheet->getCell('M' . $row)->getValue(),
                'profession' => $active_sheet->getCell('N' . $row)->getValue(),
                'workphone' => $active_sheet->getCell('O' . $row)->getValue(),
                'income' => $active_sheet->getCell('AO' . $row)->getValue(),
                'expenses' => $active_sheet->getCell('AP' . $row)->getValue(),
                'regaddress_id' => $reg_id,
                'faktaddress_id' => $fakt_id,
                'created' => date('Y-m-d H:i:s', strtotime($created))
            ];

            $this->users->add_user($user);
        }
    }

    private function import_orders()
    {
        $tmp_name = $this->config->root_dir . '/files/orders.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $id = $active_sheet->getCell('D' . $row)->getValue();

            if (empty($id))
                continue;

            $created = $active_sheet->getCell('A' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));

            $reject_reason = '';

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Отказ') {
                $reject_reason = $active_sheet->getCell('N' . $row)->getValue();
                $status = 3;
            }

            if (in_array($active_sheet->getCell('I' . $row)->getFormattedValue(), ['Выдан', 'В суде', 'Отправлена претензия', 'Передан на судебную стадию', "Подписан (дистанционно)", "Получен исполнительный лист", "У коллектора"]))
                $status = 5;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'На рассмотрении')
                $status = 1;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Оплачен' || $active_sheet->getCell('I' . $row)->getValue() === 'Списан')
                $status = 7;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Отменен')
                $status = 8;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Одобрен' || $active_sheet->getCell('I' . $row)->getValue() === 'Одобрен предварительно')
                $status = 2;


            if ($active_sheet->getCell('Q' . $row)->getValue() === 'ONLINE-0,5!')
                $loantype_id = 2;
            elseif ($active_sheet->getCell('Q' . $row)->getValue() === 'ВСЕМ-0,9!')
                $loantype_id = 3;
            else
                $loantype_id = 1;

            $loantype = $this->Loantypes->get_loantype($loantype_id);


            $new_order = [
                'outer_id' => $id,
                'date' => $created,
                'loantype_id' => $loantype_id,
                'period' => 30,
                'amount' => $active_sheet->getCell('G' . $row)->getValue(),
                'accept_date' => $created,
                'confirm_date' => $created,
                'status' => $status,
                'percent' => $loantype->percent,
                'reject_reason' => $reject_reason
            ];

            $order_id = $this->orders->add_order($new_order);

            $this->db->query("
                SELECT *
                FROM s_users
                where outer_id = ?
                ", $active_sheet->getCell('O' . $row)->getValue());

            $user = $this->db->result();

            if (!empty($user))
                $this->orders->update_order($order_id, ['user_id' => $user->id]);

        }
        exit;
    }

    private function import_contracts()
    {
        $tmp_name = $this->config->root_dir . '/files/contracts.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $created = $active_sheet->getCell('B' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));

            $issuance_date = $active_sheet->getCell('C' . $row)->getFormattedValue();
            $issuance_date = date('Y-m-d H:i:s', strtotime($issuance_date));

            $return_date = $active_sheet->getCell('E' . $row)->getFormattedValue();
            $return_date = date('Y-m-d', strtotime($return_date));

            $new_contract =
                [
                    'number' => $active_sheet->getCell('A' . $row)->getValue(),
                    'type' => 'base',
                    'period' => 30,
                    'uid' => $active_sheet->getCell('K' . $row)->getValue(),
                    'amount' => $active_sheet->getCell('F' . $row)->getValue(),
                    'status' => 0,
                    'create_date' => $created,
                    'inssuance_date' => $issuance_date,
                    'return_date' => $return_date
                ];

            $contract_id = $this->contracts->add_contract($new_contract);

            $this->db->query("
                SELECT *
                FROM s_users
                where outer_id = ?
                ", $active_sheet->getCell('N' . $row)->getValue());

            $user = $this->db->result();

            if (!empty($user))
                $this->contracts->update_contract($contract_id, ['user_id' => $user->id]);

            $this->db->query("
                SELECT *
                FROM s_orders
                where outer_id = ?
                ", $active_sheet->getCell('M' . $row)->getValue());

            $order = $this->db->result();

            $loantype = $this->Loantypes->get_loantype($order->loantype_id);
            $percent = $loantype->percent;

            $statuses = array(
                1 => 0,
                3 => 8,
                5 => 2,
                7 => 3,
                8 => 8
            );

            $new_contract =
                [
                    'order_id' => $order->id,
                    'base_percent' => $percent,
                    'status' => $statuses[$order->status]
                ];

            $this->contracts->update_contract($contract_id, $new_contract);
            $this->orders->update_order($order->id, ['contract_id' => $contract_id]);
        }
    }

    private function import_operations()
    {
        $tmp_name = $this->config->root_dir . '/files/operations.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $number = $active_sheet->getCell('B' . $row)->getValue();

            $this->db->query("
            SELECT *
            FROM s_operations
            WHERE `number` = ?
            ", $number);

            $opertion = $this->db->result();

            if (!empty($opertion))
                continue;


            $id = $active_sheet->getCell('B' . $row)->getValue();
            $created = $active_sheet->getCell('H' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));
            $type = 'P2P';
            $amount = $active_sheet->getCell('K' . $row)->getValue();

            if ($active_sheet->getCell('J' . $row)->getValue() === 'Погашение') {
                $type = 'PAY';
                $amount = $active_sheet->getCell('L' . $row)->getValue();
            }

            $this->db->query("
            SELECT *
            FROM s_contracts
            where `number` = ?
            ", $id);

            $contract = $this->db->result();

            $this->operations->add_operation([
                'contract_id' => $contract->id,
                'user_id' => $contract->user_id,
                'order_id' => $contract->order_id,
                'type' => $type,
                'amount' => $amount,
                'created' => $created
            ]);
        }
    }

    private function import_balance()
    {
        $tmp_name = $this->config->root_dir . '/files/balances.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $id = $active_sheet->getCell('B' . $row)->getValue();
            $od = $active_sheet->getCell('G' . $row)->getValue();
            $prc = $active_sheet->getCell('I' . $row)->getValue() + $active_sheet->getCell('H' . $row)->getValue();
            $peni = $active_sheet->getCell('K' . $row)->getFormattedValue();

            if ($peni == "#NULL!") {
                $peni = 0;
            }

            $contract =
                [
                    'loan_peni_summ' => (float)$peni
                ];

            $this->db->query("
            UPDATE s_contracts 
            SET ?% 
            WHERE `number` = ?
            ", $contract, $id);
        }
    }

    private function statuses()
    {
        $this->db->query("
        SELECT *
        from s_contracts
        where `status` = 3
        ");

        $contracts = $this->db->results();

        foreach ($contracts as $contract) {
            $this->db->query("
            UPDATE s_orders
            set `status` = 5
            where contract_id = ?
            ", $contract->id);
        }
    }

    private function edit_orders_amount()
    {
        $tmp_name = $this->config->root_dir . '/files/contracts.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $this->db->query("
                UPDATE s_orders
                SET `amount` = ?
                where outer_id = ?
                ", $active_sheet->getCell('F' . $row)->getValue(), $active_sheet->getCell('M' . $row)->getValue());
        }
    }

    private function import_phones()
    {
        $tmp_name = $this->config->root_dir . '/files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $outer_id = $active_sheet->getCell('P' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $phone = preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('K' . $row)->getValue());
            $phone = str_split($phone);
            $phone[0] = '7';
            $phone = implode('', $phone);

            $this->db->query("
            UPDATE s_users
            SET phone_mobile = ?
            where outer_id = ?
            ", $phone, $outer_id);
        }
    }

    private function import_prolongations()
    {
        $tmp_name = $this->config->root_dir . '/files/orders.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $fio = $active_sheet->getCell('B' . $row)->getValue();
        }
    }

    private function competeCardEnroll()
    {
        $this->db->query("
        SELECT
        ts.id,
        ts.user_id,
        ts.amount,
        ts.register_id
        FROM s_orders os
        JOIN s_transactions ts ON os.user_id = ts.user_id
        WHERE ts.`description` = 'Привязка карты'
        AND reason_code = 1
        AND os.`status` = 3
        and checked = 0
        and created > '2022-11-25 00:00:00'
        order by id desc
        ");

        $transactions = $this->db->results();

        foreach ($transactions as $transaction)
            $this->Best2pay->completeCardEnroll($transaction);
    }


}

new test();