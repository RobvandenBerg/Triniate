<?php
class GIFEncoder {
    private string $GIF = "GIF89a"; // GIF header 6 bytes
    private string $VER = "GIFEncoder V2.05"; // Encoder version
    private array $BUF = [];
    private int $LOP = 0;
    private int $DIS = 2;
    private int $COL = -1;
    private int $IMG = -1;

    private array $ERR = [
        'ERR00' => "Does not support function for only one image!",
        'ERR01' => "Source is not a GIF image!",
        'ERR02' => "Unintelligible flag",
        'ERR03' => "Cannot create animation from an animated GIF source!",
    ];

    public function __construct(
        array $GIF_src,
        array $GIF_dly,
        int $GIF_lop,
        int $GIF_dis,
        int $GIF_red,
        int $GIF_grn,
        int $GIF_blu,
        string $GIF_mod
    ) {
        if (!is_array($GIF_src) || !is_array($GIF_dly)) {
            throw new InvalidArgumentException($this->ERR['ERR00']);
        }

        $this->LOP = max($GIF_lop, 0);
        $this->DIS = max(min($GIF_dis, 3), 0);
        $this->COL = ($GIF_red > -1 && $GIF_grn > -1 && $GIF_blu > -1)
            ? ($GIF_red | ($GIF_grn << 8) | ($GIF_blu << 16))
            : -1;

        foreach ($GIF_src as $i => $src) {
            if (strtolower($GIF_mod) === "url") {
                $this->BUF[] = file_get_contents($src);
            } elseif (strtolower($GIF_mod) === "bin") {
                $this->BUF[] = $src;
            } else {
                throw new InvalidArgumentException($this->ERR['ERR02'] . " ($GIF_mod)");
            }

            if (substr($this->BUF[$i], 0, 6) !== "GIF87a" && substr($this->BUF[$i], 0, 6) !== "GIF89a") {
                throw new RuntimeException($this->ERR['ERR01']);
            }

            for (
                $j = (13 + 3 * (2 << (ord($this->BUF[$i][10]) & 0x07))), $k = true;
                $k;
                $j++
            ) {
                switch ($this->BUF[$i][$j]) {
                    case "!":
                        if (substr($this->BUF[$i], ($j + 3), 8) === "NETSCAPE") {
                            throw new RuntimeException($this->ERR['ERR03']);
                        }
                        break;
                    case ";":
                        $k = false;
                        break;
                }
            }
        }

        $this->GIFAddHeader();
        foreach ($this->BUF as $i => $buffer) {
            $this->GIFAddFrames($i, $GIF_dly[$i]);
        }
        $this->GIFAddFooter();
    }

    private function GIFAddHeader(): void {
        $cmap = 0;

        if (ord($this->BUF[0][10]) & 0x80) {
            $cmap = 3 * (2 << (ord($this->BUF[0][10]) & 0x07));
            $this->GIF .= substr($this->BUF[0], 6, 7);
            $this->GIF .= substr($this->BUF[0], 13, $cmap);
            $this->GIF .= "!\377\13NETSCAPE2.0\3\1" . $this->GIFWord($this->LOP) . "\0";
        }
    }

    private function GIFAddFrames(int $i, int $d): void {
        $localsStr = 13 + 3 * (2 << (ord($this->BUF[$i][10]) & 0x07));
        $localsEnd = strlen($this->BUF[$i]) - $localsStr - 1;
        $localsTmp = substr($this->BUF[$i], $localsStr, $localsEnd);

        $globalLen = 2 << (ord($this->BUF[0][10]) & 0x07);
        $localsLen = 2 << (ord($this->BUF[$i][10]) & 0x07);

        $globalRgb = substr($this->BUF[0], 13, 3 * $globalLen);
        $localsRgb = substr($this->BUF[$i], 13, 3 * $localsLen);

        $localsExt = "!\xF9\x04" . chr(($this->DIS << 2)) . chr($d & 0xFF) . chr(($d >> 8) & 0xFF) . "\x0\x0";

        if ($this->COL > -1 && ord($this->BUF[$i][10]) & 0x80) {
            for ($j = 0; $j < $localsLen; $j++) {
                if (
                    ord($localsRgb[3 * $j + 0]) === (($this->COL >> 16) & 0xFF) &&
                    ord($localsRgb[3 * $j + 1]) === (($this->COL >> 8) & 0xFF) &&
                    ord($localsRgb[3 * $j + 2]) === ($this->COL & 0xFF)
                ) {
                    $localsExt = "!\xF9\x04" . chr(($this->DIS << 2) + 1) . chr($d & 0xFF) . chr(($d >> 8) & 0xFF) . chr($j) . "\x0";
                    break;
                }
            }
        }

        switch ($localsTmp[0]) {
            case "!":
                $localsImg = substr($localsTmp, 8, 10);
                $localsTmp = substr($localsTmp, 18);
                break;
            case ",":
                $localsImg = substr($localsTmp, 0, 10);
                $localsTmp = substr($localsTmp, 10);
                break;
        }

        if (ord($this->BUF[$i][10]) & 0x80 && $this->IMG > -1) {
            if ($globalLen === $localsLen) {
                if ($this->GIFBlockCompare($globalRgb, $localsRgb, $globalLen)) {
                    $this->GIF .= $localsExt . $localsImg . $localsTmp;
                } else {
                    $byte = ord($localsImg[9]);
                    $byte |= 0x80;
                    $byte &= 0xF8;
                    $byte |= (ord($this->BUF[0][10]) & 0x07);
                    $localsImg[9] = chr($byte);
                    $this->GIF .= $localsExt . $localsImg . $localsRgb . $localsTmp;
                }
            } else {
                $byte = ord($localsImg[9]);
                $byte |= 0x80;
                $byte &= 0xF8;
                $byte |= (ord($this->BUF[$i][10]) & 0x07);
                $localsImg[9] = chr($byte);
                $this->GIF .= $localsExt . $localsImg . $localsRgb . $localsTmp;
            }
        } else {
            $this->GIF .= $localsExt . $localsImg . $localsTmp;
        }

        $this->IMG = 1;
    }

    private function GIFAddFooter(): void {
        $this->GIF .= ";";
    }

    private function GIFBlockCompare(string $globalBlock, string $localBlock, int $len): bool {
        for ($i = 0; $i < $len; $i++) {
            if (
                $globalBlock[3 * $i + 0] !== $localBlock[3 * $i + 0] ||
                $globalBlock[3 * $i + 1] !== $localBlock[3 * $i + 1] ||
                $globalBlock[3 * $i + 2] !== $localBlock[3 * $i + 2]
            ) {
                return false;
            }
        }
        return true;
    }

    private function GIFWord(int $int): string {
        return chr($int & 0xFF) . chr(($int >> 8) & 0xFF);
    }

    public function GetAnimation(): string {
        return $this->GIF;
    }
}
?>
