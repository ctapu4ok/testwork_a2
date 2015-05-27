<?php

class CheckBraces
{
    // Файл
    public $File;
    // $o == chr(123) == '{' and $c == chr(125) == '}'
    private $o;
    private $c;
    
    private $length;
    private $fileP;
    
    // колличество открытых и закрытых фигурных скобок
    private $bOpen;
    private $bClose;
    
    // Строка и ее длина
    private $line;
    private $lineLenght;
    
    private $step;
    private $numLine;
    private $subLineLength;
    
    public function __construct($file)
    {
        $this->o = chr(123);
		$this->c = chr(125);
        
        $file = $this->upload_file($file);
        
        $this->File = file_exists($file) ? realpath($file) : false;
        
        if(!$this->File)
            $this->Error('Файл не найден '.$file);
            
        if (($this->fileP = file($this->File)) === false) {
			$this->Error("Файл не может быть открыт ".$this->File);
		}
		$this->TotOpen = 0;
		$this->TotClose = 0;
        
    }
    
    public function countBraces()
    {
        foreach($this->fileP as $k=>$v) {
			$this->length = strlen($v);
			for ($i=0;$i<$this->length;$i++) {
				if (strpos($v{$i},$this->o) !== false) {
					$this->bOpen++;
				}
				if (strpos($v{$i},$this->c) !== false) {
					$this->bClose++;
				}	
			}
		}
		reset($this->fileP);
        
		echo "<br/>Файл: ".$this->File."<br/>Всего: `{`:".$this->bOpen." - `}`:".$this->bClose."<br/>";
    }
    
    public function analysisFile()
    {
        if($this->bOpen == $this->bClose)
        {
            echo "Файл в порядке <br/>";
        }
        else
        {
            echo "В коде допущена ошибка, идет анализ файла <br/>";
            
            foreach($this->fileP as $k => $v)
            {
                $this->line = $this->prepareLine($v);
                $this->lineLenght = strlen($this->line);
                
                for($i=0;$i<$this->lineLenght;$i++)
                {
                    if(strpos($this->line[$i],$this->o) !== false)
                    {
                        
                        $this->step = 0;
                        
                        echo "<br/>Найден символ ".$this->o.". Ln: ".($k+1)." Col: ".($i+1)."";
                        
                        $this->numLine = $k;
                        
                        
                        while (@$this->fileP[$this->numLine]) 
                        {
                            $w=false;
    						$this->fileP[$this->numLine] = (!$w) ? $this->fileP[$this->numLine] : $this->prepareLine($this->fileP[$this->numLine]);
                            
    						$this->subLineLength = strlen($this->fileP[$this->numLine]);
                            
    						$ch_start = (!$w) ? $i : 0;
                            
    						for ($j=$ch_start;$j<$this->subLineLength;$j++) 
                            {
    							$w=true;
                                
    							if (strpos($this->fileP[$this->numLine]{$j},$this->o) !== false) 
                                {
    								$this->step++;
    							}
    							if (strpos($this->fileP[$this->numLine]{$j},$this->c) !== false) 
                                {
    								$this->step--;
    								if ($this->step==0) 
                                    {
    									break;								
    								}
    							}
    						}
    						if ($this->step==0) {
    							echo " - Найден символ ".$this->c." Ln ".($this->numLine+1)." Col ".($j+1)."<br/>";
    							break 1;	
    						}
    						$this->numLine++;
    					}
                        if($this->step > 0)
                        {
                            $block = '';
                            if(isset($this->fileP[$k-6]))
                                $block .= ($k-5).": ".$this->fileP[$k-6];
                            if(isset($this->fileP[$k-5]))
                                $block .= ($k-4).": ".$this->fileP[$k-5];
                            if(isset($this->fileP[$k-4]))
                                $block .= ($k-3).": ".$this->fileP[$k-4];
                            if(isset($this->fileP[$k-3]))
                                $block .= ($k-2).": ".$this->fileP[$k-3];
                            if(isset($this->fileP[$k-2]))
                                $block .= ($k-1).": ".$this->fileP[$k-2];
                            if(isset($this->fileP[$k-1]))
                                $block .= ($k).": ".$this->fileP[$k-1];
                                
                            $block .= "<span style='color:red;'>".($k+1).": ".$this->fileP[$k]."</span>";
                            
                            if(isset($this->fileP[$k+1]))
                                $block .= ($k+2).": ".$this->fileP[$k+1];
                            if(isset($this->fileP[$k+2]))
                                $block .= ($k+3).": ".$this->fileP[$k+2];
                            if(isset($this->fileP[$k+3]))
                                $block .= ($k+4).": ".$this->fileP[$k+3];
                            if(isset($this->fileP[$k+4]))
                                $block .= ($k+5).": ".$this->fileP[$k+4];
                            if(isset($this->fileP[$k+5]))
                                $block .= ($k+6).": ".$this->fileP[$k+5];
                            if(isset($this->fileP[$k+6]))
                                $block .= ($k+7).": ".$this->fileP[$k+6];
                            echo "<br/> Возможно где то тут ошибка: ".$this->pr($block)."<br/>";
                            break;
                        }
    					$w=false;
                    }
                }
            }
        }
    }
    
    private function upload_file($file)
    {
        try {
            
            if (
                !isset($_FILES['File']['error']) ||
                is_array($_FILES['File']['error'])
            ) 
            {
                throw new Exception('Неверные параметры.');
            }
        

            switch ($_FILES['File']['error']) 
            {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception('Файл не найден.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception('Превышен размер файла.');
                default:
                    throw new Exception('Не опознаная ошибка.');
            }
        
            
            if ($_FILES['File']['size'] > 1000000) 
            {
                throw new Exception('Превышен размер файла.');
            }

            if (false === $ext = array_search($_FILES['File']['type'],
                array(
                    'css' => 'text/css',
                    'php' => 'application/x-httpd-php',
                ),
                true
            )) 
            {
                throw new Exception('Неверный формат файла.');
            }

            if (!move_uploaded_file($_FILES['File']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/upload/'.$_FILES['File']['name'])) 
            {
                throw new Exception('Не возможно загрузить файл.');
            }
        
            return $_SERVER['DOCUMENT_ROOT'].'/upload/'.$_FILES['File']['name'];
        
        } catch (Exception $e) {
        
            echo $e->getMessage();
        
        }
    }
    
    private function prepareLine($line) 
    {
		$tmpL = str_replace(chr(9), chr(32).chr(32).chr(32).chr(32), $line, $matches);
		
		$tmpL = rtrim($tmpL, " \r\n\t");
		return $tmpL;
	}
    
    private function Error($msg)
    {
        throw new Exception('Ошибка: '.$msg.'<br/>');
    }
    
    private function pr($code)
    {
        $text = '<div style="border: 1px solid #999; padding-left:5px;">';
        $text .=  '<pre>';
        $text .= ($code);
        $text .= '</pre>';
        $text .= '</div>';
        return $text;
    }
}
try
{
    $file1 = new CheckBraces($_FILES);
    $file1->countBraces();
    $file1->analysisFile();    
}
catch(exception $e)
{
        echo "<br>".$e->getMessage()."</br>";
}
