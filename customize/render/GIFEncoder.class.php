<?php
class GIFEncoder {
    public $frames = array(); // Array to store individual frame images
    public $framed = array(0);   // Array to store each frame's duration in milliseconds
    public $loopCount;                   // Number of times the animation loops
    public $disposal;                     // Disposal method for each frame
    public $transparentColorIndex;         // Index of the transparent color
    public $sourceType;                     // Source type for each frame (1: normal, 2: transparent)

    /**
     * @param array $frames Array of individual frame images in GIF format
     * @param int[] $framed Array containing each frame's duration in milliseconds
     * @param int $loopCount Number of times the animation loops
     * @param int $disposal Disposal method for each frame
     * @param array $transparentColorIndex Index of the transparent color for each frame
     * @param int $sourceType Source type for each frame (1: normal, 2: transparent)
     */
    function __construct($frames, $framed, $loopCount, $disposal, $transparentColorIndex, $sourceType) {
        $this->frames = $frames;
        $this->framed = $framed;
        $this->loopCount = $loopCount;
        $this->disposal = $disposal;
        $this->transparentColorIndex = $transparentColorIndex;
        $this->sourceType = $sourceType;
    }

    /**
     * Generate and output the GIF animation from the stored frames and durations
     */
    public function GetAnimation() {
        // Header ( 'Content-type:image/gif' )
        echo "Content-type:image/gif\n";

        // Add GIF header
        $this->AddGIFHeader();

        foreach ($this->frames as $frame) {
            $this->AddFrame($frame);
        }

        // Terminate GIF stream
        $this->EndGIFStream();
    }

    /**
     * Add the GIF header to the output stream
     */
    private function AddGIFHeader() {
        echo "\x21\xF9\x04";  // GIF signature
    }

    /**
     * Add a frame to the GIF animation stream
     * @param string $frame The GIF frame image data
     */
    private function AddFrame($frame) {
        echo "\x2C\x00";  // Block Specifier (Local Color Table)
        echo pack("v", 8);   // Block Size (size of color table in bytes)

        $colorTable = unpack('V*', $this->GetColorTable());
        foreach ($colorTable as $colorCode) {
            echo pack('V', $colorCode);  // Color table data
        }

        echo "\x21\xF9\x01";  // Block Specifier (Image Description)
        echo pack("v", 3);   // Block Size
        echo pack("V", 0x4546546, 0x000801, $this->transparentColorIndex * 257);  // Image Descriptor data

        echo "\x2C\x00";  // Block Specifier (Local Color Table)
        echo pack("v", 8);   // Block Size (size of color table in bytes)

        $colorTable = unpack('V*', $this->GetColorTable());
        foreach ($colorTable as $colorCode) {
            echo pack('V', $colorCode);  // Color table data
        }

        echo "\x21\xF9\x01";  // Block Specifier (Image Description)
        echo pack("v", 3);   // Block Size
        echo pack("V", 0x4546546, 0x000801, $this->transparentColorIndex * 257);  // Image Descriptor data
    }

    /**
     * Get the color table for the GIF animation
     * @return string The color table in packed binary format
     */
    private function GetColorTable() {
        $colorTable = array(0, 0, 0); // Color table data
        return pack("V*", ...$colorTable);
    }

    /**
     * Terminate the GIF stream by adding the GIF trailer
     */
    private function EndGIFStream() {
        echo "\x21\xF9\x06";  // Block Specifier (Terminal)
        echo pack("V", 0x4546546);  // Image Descriptor data
        echo pack("v", 0);   // Image Data size in bytes
    }
}
?>