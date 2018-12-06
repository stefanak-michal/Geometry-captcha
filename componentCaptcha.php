<?php
/**
 * componentCaptcha
 * 
 * @author Michal Stefanak
 * @link https://github.com/stefanak-michal/Geometry-captcha
 */
class componentCaptcha
{
    /**
     * Triangle
     */
    const GEOMETRY_TRIANGLE = 1;
    /**
     * Rectangle
     */
    const GEOMETRY_RECTANGLE = 2;

    /**
     * SK: Ci sa maju prepocitat suradnice pre menovku pri otoceni obrazka<br>
     * EN: Recalculate coordinates after rotate image
     *
     * @access private
     * @var boolean
     */
    private $rotateLabel = true;
    /**
     * SK: Zobrazovanie debug informacii (cervene pismo)<br>
     * EN: Show debug info (red font)
     *
     * @access private
     * @var boolean
     */
    private $debug = false;
    /**
     * SK: Ci sa ma generovat aj otocenie obrazka<br>
     * EN: Rotate image
     * 
     * @access private
     * @var boolean
     */
    private $rotateImage = true;
    /**
     * SK: Povoli pridanie podpisu do obrazka<br>
     * EN: Add subscribe to image
     *
     * @access private
     * @var boolean
     */
    private $allowSubscribe = true;
    /**
     * SK: Zakladny rozmer obrazka<br>
     * EN: Base size of generated image
     *
     * @access private
     * @var int
     */
    private $baseSize = 300;
    /**
     * SK: Definicie potrebnych menoviek na vykreslenie pre konkretne hodnoty<br>
     * EN: Definition of labels for draw specific values
     *
     * @access private
     * @var array
     */
    private $requirements = array(
        self::GEOMETRY_TRIANGLE => array(
            'x' => array('y', 'z'), 
            'y' => array('x', 'z'), 
            'z' => array('x', 'y'), 
            'a' => array('b'), 
            'b' => array('a'), 
            'o' => array('x', 'y', 'z'), 
            's' => array('x', 'y')
        ),
        self::GEOMETRY_RECTANGLE => array(
            'x' => array('y', 'z', 'v'), 
            'y' => array('x', 'z', 'v'), 
            'z' => array('x', 'y', 'v'), 
            'v' => array('x', 'y', 'z'), 
            'a' => array('b', 'c', 'd'), 
            'b' => array('a', 'c', 'd'), 
            'c' => array('a', 'b', 'd'), 
            'd' => array('a', 'b', 'c'), 
            'o' => array('x', 'y', 'z', 'v'), 
            's' => array('x', 'y')
        ),
    );
    
    /**
     * SK: Vytvoreny obrazok<br>
     * EN: Drawed image
     *
     * @access private
     * @var gd
     */
    private $image;
    
    /**
     * constructor
     */
    public function __construct()
    {
        
    }
    
    /**
     * SK: Nastavenie zakladnej velkosti obrazka<br>
     * EN: Set base size of image
     * 
     * @param int $size
     */
    public function setSize($size)
    {
        if (is_int($size) AND $size >= 100)
        {
            $this->baseSize = $size;
        }
    }
    
    /**
     * SK: Ci sa ma generovat otocenie obrazka<br>
     * EN: Set of rotate drawed image
     * 
     * @param boolean $rotate
     */
    public function setRotate($rotate)
    {
        $this->rotateImage = $this->rotateLabel = (boolean) $rotate;
    }
    
    /**
     * SK: Ci generovat do obrazka aj debug hodnoty<br>
     * EN: Set of debug info
     * 
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (boolean) $debug;
    }
    
    /**
     * SK: Uvodna metoda ktora vyberie nahodnu geometrie a zavola co treba<br>
     * EN: Base method to choose random geometry a call what need
     * 
     * @return int|float Result
     */
    public function create()
    {
        $output = 0;
        
        if (rand(0,1) == 1)
        {
            $output = $this->drawTriangle();
        }
        else
        {
            $output = $this->drawRectangle();
        }
        
        return $output;
    }
    
    /**
     * SK: Priame vykreslenie obrazka<br>
     * EN: Direct show image
     */
    public function show()
    {
        $this->subscribe();
        
        header('Content-type: image/png');
        imagejpeg($this->image);
        
        imagedestroy($this->image);
    }
    
    /**
     * SK: Ulozenie obrazka<br>
     * EN: Save image to file
     * 
     * @param string $path Filepath with filename
     * @return boolean
     */
    public function save($path)
    {
        $this->subscribe();
        
        $result = imagejpeg($this->image, $path);
        imagedestroy($this->image);
        
        return $result;
    }
    
    /**
     * SK: Prida podpis do obrazka<br>
     * EN: Add subscribe to image
     * 
     * @access private
     */
    private function subscribe()
    {
        if ($this->allowSubscribe)
        {
            $rotate = $this->rotateLabel;
            $this->rotateLabel = false;
            imagestring($this->image, 1, imagesx($this->image) - 150, imagesy($this->image) - 10, 'Powered by Geometry captcha', imagecolorallocate($this->image, 100, 100, 100));
            $this->rotateLabel = $rotate;
        }
    }
    
    /**
     * SK: Vykreslenie trojuholnika<br>
     * EN: Draw triangle
     * 
     * @access private
     * @return int|float Result
     */
    private function drawTriangle()
    {
        //zakladne hodnoty geometrie
        //base geometry values
        $c = array(
            'size' => array(
                'x' => $this->baseSize,
            ),
            'angle' => $this->rotateImage ? rand(0, 36) : 0
        );
        
        //obrazok musi byt vzdy stvorcovy
        //image must be always square
        $c['size']['y'] = $c['size']['x'];
        
        $c['x'] = rand( round($c['size']['x'] / 10 * 0.4), round($c['size']['x'] / 10 * 0.7) ) * 10;
        $c['y'] = rand( round($c['size']['y'] / 10 * 0.4), round($c['size']['y'] / 10 * 0.7) ) * 10;
        $c['angle'] = $c['angle'] * 10;

        //zakladna matematika
        //base math
        $c['z'] = sqrt( $c['x'] * $c['x'] + $c['y'] * $c['y'] );

        $c['C']['x'] = $c['B']['x'] = $c['size']['x'] / 2 - $c['x'] / 2;
        $c['C']['y'] = $c['A']['y'] = $c['size']['y'] / 2 - $c['y'] / 2;
        $c['A']['x'] = $c['C']['x'] + $c['x'];
        $c['B']['y'] = $c['C']['y'] + $c['y'];

        $c['a'] = rad2deg( acos($c['x'] / $c['z']) );
        $c['b'] = rad2deg( acos($c['y'] / $c['z']) );
        $c['c'] = 90;
        
        $c['o'] = $c['x'] + $c['y'] + $c['z'];
        $c['s'] = $c['x'] * $c['y'] / 2;

        $c['cos'] = cos( deg2rad($c['angle']) );
        $c['sin'] = sin( deg2rad($c['angle']) );
        
        //menovky .. suradnice voci stredu
        //labels .. coordinates towards midpoint
        $c['label']['x']['x'] = 0;
        $c['label']['x']['y'] = $c['y'] / 2 + 15;
        $c['label']['y']['x'] = -$c['x'] / 2 - 15;
        $c['label']['y']['y'] = 0;
        $c['label']['z']['x'] = 10;
        $c['label']['z']['y'] = -10;
        $c['label']['a']['x'] = $c['x'] / 2 - 15;
        $c['label']['a']['y'] = $c['y'] / 2 - 15;
        $c['label']['b']['x'] = -$c['x'] / 2 + 15;
        $c['label']['b']['y'] = -$c['y'] / 2 + 15;
        $c['label']['corner']['x'] = $c['label']['o']['x'] = $c['label']['s']['x'] = -$c['size']['x'] / 2 + $c['size']['x'] * 0.05;
        $c['label']['corner']['y'] = $c['label']['o']['y'] = $c['label']['s']['y'] = $c['size']['y'] / 2 - $c['size']['y'] * 0.05;

        //vygenerovanie obrazka
        //generate image
        $image = $this->createImage($c);
        
        //trojuholnik
        //triangle
        imagepolygon($image, array(
        $c['C']['x'], $c['C']['y'], $c['A']['x'], $c['A']['y'], $c['B']['x'], $c['B']['y']
        ), 3, $c['color']['shapes']);

        //pravy uhol
        //right angle
        imagearc($image, $c['C']['x'], $c['C']['y'], $c['size']['x'] * 0.13, $c['size']['x'] * 0.13, 0, 90, $c['color']['shapes']);
        imagerectangle($image, $c['C']['x'] + $c['size']['x'] * 0.026, $c['C']['y'] + $c['size']['y'] * 0.026, $c['C']['x'] + $c['size']['x'] * 0.029, $c['C']['y'] + $c['size']['y'] * 0.029, $c['color']['shapes']);
        //ostatne uhly
        //another angles
        imagearc($image, $c['A']['x'], $c['A']['y'], $c['size']['x'] * 0.2, $c['size']['x'] * 0.2, 180 - $c['a'], 180, $c['color']['shapes']);
        imagearc($image, $c['B']['x'], $c['B']['y'], $c['size']['x'] * 0.2, $c['size']['x'] * 0.2, 270, 270 + $c['b'], $c['color']['shapes']);

        //otocenie
        //rotate
        $croped = $this->rotateImage($image, $c);
        
        //pridanie menoviek
        //add labels
        $output = $this->createCaptchaLabels(self::GEOMETRY_TRIANGLE, $croped, $c);
        
        $this->image = $croped;
        
        return $output;
    }
    
    /**
     * SK: Vykreslenie kosostvorca<br>
     * EN: Draw rectangle
     * 
     * @access private
     * @return int|float Result
     */
    private function drawRectangle()
    {
        //zakladne hodnoty geometrie
        //base geometry values
        $c = array(
            'size' => array(
                'x' => $this->baseSize,
            ),
            'angle' => $this->rotateImage ? rand(0, 36) : 0
        );
        
        //obrazok musi byt vzdy stvorcovy
        //image must be always square
        $c['size']['y'] = $c['size']['x'];
        //nahodne hodnoty
        //random values
        $c['x'] = $c['v'] = rand( round($c['size']['x'] / 10 * 0.3), round($c['size']['x'] / 10 * 0.5) ) * 10;
        $c['y'] = $c['z'] = rand( round($c['size']['y'] / 10 * 0.3), round($c['size']['y'] / 10 * 0.5) ) * 10;
        $c['b'] = rand(60, 120);
        $c['angle'] = $c['angle'] * 10;
        
        //zakladna matematika
        //base math
        $c['A']['x'] = $c['size']['x'] / 2 - $c['x'] / 2;
        $c['A']['y'] = $c['size']['y'] / 2 - $c['y'] / 2;
        $c['B']['x'] = $c['A']['x'] + $c['x'];
        $c['B']['y'] = $c['A']['y'];
        $c['C']['x'] = $c['B']['x'] + $c['y'] * cos( deg2rad($c['b']) );
        $c['C']['y'] = $c['B']['y'] + $c['y'] * sin( deg2rad($c['b']) );
        $c['D']['x'] = $c['C']['x'] - $c['x'];
        $c['D']['y'] = $c['C']['y'];
        
        $move = ($c['A']['x'] - $c['D']['x']) / 2;
        $c['A']['x'] += $move;
        $c['B']['x'] += $move;
        $c['C']['x'] += $move;
        $c['D']['x'] += $move;
        
        $c['a'] = $c['c'] = $c['b'];
        $c['b'] = $c['d'] = 180 - $c['a'];
        
        $c['o'] = $c['x'] + $c['y'] + $c['z'] + $c['v'];
        $c['s'] = $c['x'] * $c['y'];
        
        $c['cos'] = cos( deg2rad($c['angle']) );
        $c['sin'] = sin( deg2rad($c['angle']) );
        
        //menovky .. suradnice voci stredu
        //labels .. coordinates towards midpoint
        $c['label']['x']['x'] = $move;
        $c['label']['x']['y'] = $c['y'] / 2 + 15;
        $c['label']['y']['x'] = -$c['x'] / 2 - 10;
        $c['label']['y']['y'] = 0;
        $c['label']['z']['x'] = $c['x'] / 2 + 10;
        $c['label']['z']['y'] = 0;
        $c['label']['v']['x'] = -$move;
        $c['label']['v']['y'] = -$c['label']['x']['y'] + abs($move);// + $c['size']['x'] * 0.02;
        
        $c['label']['a']['x'] = -$c['x'] / 2 + $move + 10;
        $c['label']['a']['y'] = $c['y'] / 2 - 5;
        $c['label']['b']['x'] = $c['x'] / 2 + $move - 15;
        $c['label']['b']['y'] = $c['label']['a']['y'];
        $c['label']['c']['x'] = $c['label']['b']['x'] - ($c['A']['x'] - $c['D']['x']);
        $c['label']['c']['y'] = -$c['y'] / 2 + abs($move) + $c['size']['x'] * 0.02;
        $c['label']['d']['x'] = $c['label']['a']['x'] - ($c['A']['x'] - $c['D']['x']);
        $c['label']['d']['y'] = $c['label']['c']['y'];
        
        $c['label']['corner']['x'] = $c['label']['o']['x'] = $c['label']['s']['x'] = -$c['size']['x'] / 2 + $c['size']['x'] * 0.05;
        $c['label']['corner']['y'] = $c['label']['o']['y'] = $c['label']['s']['y'] = $c['size']['y'] / 2 - $c['size']['y'] * 0.05;
        
        //vygenerovanie obrazka
        //generate image
        $image = $this->createImage($c);
        
        //kosostvorec
        //rectangle
        imagepolygon($image, array(
        $c['A']['x'], $c['A']['y'], $c['B']['x'], $c['B']['y'], $c['C']['x'], $c['C']['y'], $c['D']['x'], $c['D']['y']
        ), 4, $c['color']['shapes']);
        
        //uhly
        //angles
        imagearc($image, $c['A']['x'], $c['A']['y'], $c['size']['x'] * 0.2, $c['size']['x'] * 0.2, 0, $c['a'], $c['color']['shapes']);
        imagearc($image, $c['B']['x'], $c['B']['y'], $c['size']['x'] * 0.2, $c['size']['x'] * 0.2, 180 - $c['b'], 180, $c['color']['shapes']);
        imagearc($image, $c['C']['x'], $c['C']['y'], $c['size']['x'] * 0.2, $c['size']['x'] * 0.2, 180, 180 + $c['c'], $c['color']['shapes']);
        imagearc($image, $c['D']['x'], $c['D']['y'], $c['size']['x'] * 0.2, $c['size']['x'] * 0.2, 360 - $c['d'], 360, $c['color']['shapes']);

        //otocenie
        //rotate
        $croped = $this->rotateImage($image, $c);
        
        //pridanie menoviek
        //add labels
        $output = $this->createCaptchaLabels(self::GEOMETRY_RECTANGLE, $croped, $c);
        
        $this->image = $croped;
        
        return $output;
    }
    
    /**
     * SK: Vytvorenie menoviek v obrazku<br>
     * EN: Draw labels in image
     * 
     * @access private
     * @param int $geometry
     * @param gd $image
     * @param array $c
     * @return int|float Result
     */
    private function createCaptchaLabels($geometry, $image, $c)
    {
        $type = array_rand($this->requirements[$geometry]);
        
        if ($this->debug)
        {
            imagestring($image, 2, 2, 0, $type . ' ' . $c['label'][$type]['x'] . ' ' . $c['label'][$type]['y'], $c['color']['red']);
        }
        
        switch ($geometry)
        {
            case self::GEOMETRY_TRIANGLE:
                $this->triangleMainLabels($image, $type, $c);
                break;
            case self::GEOMETRY_RECTANGLE:
                $this->rectangleMainLabels($image, $type, $c);
                break;
        }
        
        foreach ($this->requirements[$geometry][$type] AS $req)
        {
            if ($this->debug)
            {
                if ( ! isset($i))
                {
                    $i = 1;
                }
                
                imagestring($image, 2, 2, 15 * $i, $req . ' ' . $c['label'][$req]['x'] . ' ' . $c['label'][$req]['y'] . ' ' . $c[$req], $c['color']['red']);
                $i++;
            }
            
            $this->drawLabel($image, $c, $c['label'][$req]['x'], $c['label'][$req]['y'], $req . ' = ' . round($c[$req]));
        }
        
        return $c[$type];
    }
    
    /**
     * SK: Vytvori specificke menovky pre trojuholnik<br>
     * EN: Draw triangle specific labels
     * 
     * @access private
     * @param gd $image
     * @param string $type
     * @param array $c
     */
    private function triangleMainLabels($image, $type, $c)
    {
        switch ($type)
        {
            case 'x':
            case 'y':
            case 'z':
                $rotate = $this->rotateLabel;
                $this->rotateLabel = false;
                $this->drawLabel($image, $c, $c['label']['corner']['x'], $c['label']['corner']['y'], 'x + y + z = ' . round($c['o']));
                $this->rotateLabel = $rotate;
                $this->drawLabel($image, $c, $c['label'][$type]['x'], $c['label'][$type]['y'], $type . ' = ?');
                break;
            
            case 'a':
            case 'b':
                $this->drawLabel($image, $c, $c['label'][$type]['x'], $c['label'][$type]['y'], $type . ' = ?');
                break;
            
            case 'o':
            case 's':
                $rotate = $this->rotateLabel;
                $this->rotateLabel = false;
                $this->drawLabel($image, $c, $c['label']['corner']['x'], $c['label']['corner']['y'], $type == 'o' ? 'x + y + z = ?' : 'x * y / 2 = ?');
                $this->rotateLabel = $rotate;
                break;
        }
    }
    
    /**
     * SK: Vytvori specificke menovky pre kosostvorec<br>
     * EN: Draw rectangle specific labels
     * 
     * @access private
     * @param gd $image
     * @param string $type
     * @param array $c
     */
    private function rectangleMainLabels($image, $type, $c)
    {
        switch ($type)
        {
            case 'x':
            case 'y':
            case 'z':
            case 'v':
            case 'a':
            case 'b':
            case 'c':
            case 'd':
                $this->drawLabel($image, $c, $c['label'][$type]['x'], $c['label'][$type]['y'], $type . ' = ?');
                break;
            
            case 'o':
            case 's':
                $rotate = $this->rotateLabel;
                $this->rotateLabel = false;
                $this->drawLabel($image, $c, $c['label']['corner']['x'], $c['label']['corner']['y'], $type == 'o' ? 'x + y + z + v = ?' : 'x * y = ?');
                $this->rotateLabel = $rotate;
                break;
        }
    }
    
    /**
     * SK: Vytvorenie obrazka<br>
     * EN: Base create of image
     * 
     * @access private
     * @param array $c
     * @return gd
     */
    private function createImage( & $c)
    {
        $image = imagecreatetruecolor($c['size']['x'], $c['size']['y']);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        
        $c['color']['shapes'] = imagecolorallocate($image, 100, 100, 100);
        $c['color']['blue'] = imagecolorallocate($image, 0, 0, 255);
        $c['color']['red'] = imagecolorallocate($image, 255, 0, 0);
        
        return $image;
    }
    
    /**
     * SK: Otocenie a orezanie obrazka<br>
     * EN: Rotate and crop image
     * 
     * @access private
     * @param gd $image
     * @param array $c
     * @return gd
     */
    private function rotateImage($image, $c)
    {
        $rotated = imagerotate($image, $c['angle'], imagecolorallocate($image, 255, 255, 255));
        $croped = imagecreatetruecolor($c['size']['x'], $c['size']['y']);
        imagecopy($croped, $rotated, 0, 0, imagesx($rotated) / 2 - $c['size']['x'] / 2, imagesy($rotated) / 2 - $c['size']['y'] / 2, $c['size']['x'], $c['size']['y']);
        
        if ($this->debug)
        {
            imagestring($croped, 2, 2, $c['size']['y'] - 15, $c['angle'] . '°', $c['color']['red']);
        }
        
        return $croped;
    }
    
    /**
     * SK: Vlozenie menovky ku geometrii<br>
     * Prepocitava suradnice z povodneho stredu obrazka na lavy horny roh<br>
     * EN: Insert label to geometry<br>
     * Recalculate coordinates from original midpoint to upper left corner
     * 
     * @access private
     * @param gd $image
     * @param array $c
     * @param int|float $x
     * @param int|float $y
     * @param string $text
     */
    private function drawLabel($image, $c, $x, $y, $text)
    {
        if ($this->rotateImage AND $this->rotateLabel)
        {
            $rx = $x * $c['cos'] - $y * $c['sin'];
            $ry = $x * $c['sin'] + $y * $c['cos'];

            $label = array(
                'x' => $c['size']['x'] / 2 + $rx,
                'y' => $c['size']['y'] / 2 - $ry
            );
        }
        else
        {
            $label = array(
                'x' => $c['size']['x'] / 2 + $x,
                'y' => $c['size']['y'] / 2 - $y
            );
        }
        
        imagestring($image, 3, $label['x'], $label['y'], $text, $c['color']['blue']);
    }
    
}
