<?php

class App
{
    private $db;
    private $html;
    
    private $params = array();
    private $request = array();
    
    public function __construct()
    {
        $this->db = Autoload::getClass('database');
        $this->html = Autoload::getClass('html');
        
        $this->params = Autoload::getConfig();
        $this->request = Autoload::getRequest();
        
        $this->index();
    }
    
    public function index()
    {
        if(isset($this->request['parse']))
            $this->parse();
            
        $this->db->fetchAll("SELECT * FROM `torg_db` ORDER BY `id` DESC LIMIT 20");
        
        Autoload::getView('index', array(
            'Result' => $this->db->getResult(),
        ));
    }
    
    public function parse()
    {   
        $html = $this->html->file_get_html($this->params['torg_site'].'/ru/catalog/programmisty-it-internet-rezyume');
        $totalPages=-1;
        
        foreach($html->find('div[class="pages"] a') as $pages)
        {
            $totalPages++;
        }
        for($i=1; $i<=$totalPages; $i++)
        {
            $html = $this->html->file_get_html($this->params['torg_site'].'/ru/catalog/programmisty-it-internet-rezyume/page/'.$i);

            foreach($html->find('table[class="offers blue offers"] tbody tr td[class="greedy summary"]') as $tr)
            {
                $href = $tr->find('a', 0)->href;
                
                $hrefts = $this->params['torg_site'].$href;
                
                $this->db->fetch("SELECT `id` FROM `torg_db` WHERE `href` = '".$hrefts."' LIMIT 1");
                $checkValue = $this->db->getResult();
 
                if(!$checkValue)
                {
                
                    $ProfileHtml = $this->html->file_get_html($this->params['torg_site'].$href); 
    
                    $title = $ProfileHtml->find('div[class="blue-tube"] div[class="summary"] h2',0)->plaintext;
                    $details = array();
                    foreach($ProfileHtml->find('div[class="blue-tube"] table[class="details"] tr') as $detail)
                    {
                        $parametrs =  $detail->find('td[class="parameter"]', 0)->plaintext;
                        $value =  $detail->find('td[class="value"]', 0)->plaintext;
                        
                        if(!empty($parametrs) && !empty($value))                                        
                            $details[] = array(
                                'param' => $parametrs,
                                'value' => $value,
                            );
                    }
                    
                    $details = serialize($details);
                    $contacts = array();
                    foreach($ProfileHtml->find('div[class="blue-container contacts"] table[class="details"] tr') as $contact)
                    {
                        $parametrs =  $contact->find('td[class="parameter"]', 0)->plaintext;
                        $value =  $contact->find('td[class="value"]', 0)->plaintext;
                        
                        if(!empty($parametrs) && !empty($value))                                        
                            $contacts[] = array(
                                'param' => $parametrs,
                                'value' => $value,
                            );
                    }
                    $contacts = serialize($contacts);
                    
                    $this->db->sql("INSERT INTO `torg_db` 
                                    (`title`, `details`, `contacts`, `href`) VALUES 
                                    ('".$title."', '".$details."', '".$contacts."', '".$hrefts."')
                    ");
                }
            }
        }
    }
}