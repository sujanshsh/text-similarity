<?php

/**
 * 
 * @author Sujan Shakya
 *
 */

class Text_similarity
{

    public function getSimilarity($s1, $s2)
    {
        $p1 = $this->get_similarity_by_words($s1, $s2);
        $p2 = $this->get_similarity_characters($s1, $s2);
        return $p1 >= $p2 ? $p1 : $p2;
    }

    private function get_similarity_by_words($s1, $s2)
    {
        $str1 = explode(' ', preg_replace('/\s+/', ' ', preg_replace('/[\?!,;\.]/', '', strtolower($s1))));
        $str2 = explode(' ', preg_replace('/\s+/', ' ', preg_replace('/[\?!,;\.]/', '', strtolower($s2))));
        $matches = [];
        $word_matches = [];
        $all_word_count = 0;
        $word_count = 0;
        $max_word_start = 0;
        $max_word_end = 0;
        $strlen = [];
        foreach ($str1 as $w1) {
            if($w1=='')
                continue;
            $max_word_start = $all_word_count;
            foreach ($str2 as $w2) {
                if($w2=='')
                    continue;
                $matches[$all_word_count] = 0;
                $st1 = 0;
                $st2 = 0;
                $l1 = strlen($w1);
                $l2 = strlen($w2);
                for ($i = 0; $i < $l1; $i ++) {
                    for ($j = $st2; $j < $l2; $j ++) {
                        if ($w1[$i] == $w2[$j]) {
                            $matches[$all_word_count] += 1;
                            $st1 = $i + 1;
                            $st2 = $j + 1;
                            break;
                        }
                    }
                }
                $matches[$all_word_count] = (2 * $matches[$all_word_count]) / ($l1 + $l2);
                $all_word_count ++;
            }
            $max_word_end = $all_word_count - 1;
            $strlen[$word_count] = strlen($w1);
            $word_matches[$word_count ++] = $this->get_max($matches, $max_word_start, $max_word_end);
        }
        $sum = 0;
        $count = 0;
        foreach ($word_matches as $i => $m) {
            $sum += $m * $strlen[$i];
            $count += $strlen[$i];
        }
        return (100 * $sum) / $count;
    }

    private function get_similarity_characters($s1, $s2)
    {
        $str1 = preg_replace('/(\s+|[\?!,;\.])/', '', strtolower($s1));
        $str2 = preg_replace('/(\s+|[\?!,;\.])/', '', strtolower($s2));
        $matches = 0;
        $l1 = strlen($str1);
        $l2 = strlen($str2);
        $st2 = 0;
        for ($i = 0; $i < $l1; $i ++) {
            for ($j = $st2; $j < $l2; $j ++) {
                if ($str1[$i] == $str2[$j]) {
                    $matches ++;
                    $st2 = $j + 1;
                    break;
                }
            }
        }
        return (200 * $matches) / ($l1 + $l2);
    }

    private function get_max(&$matches, $max_word_start, $max_word_end)
    {
        $max = $matches[$max_word_start];
        for ($i = $max_word_start + 1; $i <= $max_word_end; $i ++) {
            if ($max < $matches[$i])
                $max = $matches[$i];
        }
        return $max;
    }
}
