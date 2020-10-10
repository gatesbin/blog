<?php

namespace TechOnline\Utils\Filter;


class BloomFilter implements Filter
{
    const NAME = 'bloomFilter';

        private $m;
        private $n;
        private $k;
    
    
    function __construct($maxN, $m)
    {
        $this->m = $m;
        $this->n = $maxN;
        $this->k = ceil(($this->m / $this->n) * log(2));
        $this->bitset = array_fill(0, ceil($this->m / 32), 0);
    }

    private $bitset = null;

    public static function build($maxN, $m = null)
    {
        if ($m === null) {
                                                $m = $maxN * 20;
        }
        return new BloomFilter($maxN, $m);
    }

    private function hashCodes($str)
    {
        $res = array();
        $seed = crc32($str);
                                mt_srand($seed);
        for ($i = 0; $i < $this->k; $i++) {
            $res[] = mt_rand(0, $this->m - 1);
        }
        return $res;
    }

    public function save($file)
    {
        $f = fopen($file, 'w+');
        foreach ($this->bitset as $value) {
            echo $value . ' ';
            fwrite($f, pack('i', $value));
        }
        fclose($f);
    }

    public function restore($file)
    {
        $f = fopen($file, 'r');
        foreach ($this->bitset as $index => $value) {
            $d = fread($f, 4);
            $d = unpack('i', $d);
            $this->bitset[$index] = $d[1];
        }
        fclose($f);
    }

    public function add($key)
    {
        $hashes = $this->hashCodes($key);
        foreach ($hashes as $codeBit) {
            $offset = intval($codeBit / 32);
            $bit = $codeBit % 32;
            $this->bitset[$offset] |= (1 << $bit);
        }
    }

    public function has($key)
    {
        $hashes = $this->hashCodes($key);
        foreach ($hashes as $codeBit) {
            $offset = intval($codeBit / 32);
            $bit = $codeBit % 32;
            if (!($this->bitset[$offset] & (1 << $bit))) {
                return false;
            }
        }
        return true;
    }
}