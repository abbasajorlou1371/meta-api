<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Secure File Upload Validation Rule
 *
 * This rule provides multi-layer security validation for file uploads:
 * 1. Extension validation - checks the file extension
 * 2. MIME type validation - validates the declared MIME type
 * 3. Magic bytes validation - verifies actual file content signatures
 * 4. Double extension prevention - blocks files like "image.php.jpg"
 * 5. Null byte injection prevention - blocks null bytes in filenames
 * 6. File size validation - enforces maximum file size limits
 *
 * @example
 * // In a FormRequest:
 * 'avatar' => ['required', new SecureFileUpload('images')]
 * 'document' => ['required', new SecureFileUpload('documents')]
 * 'attachment' => ['required', new SecureFileUpload('all')]
 * 'custom' => ['required', new SecureFileUpload(['pdf', 'docx'], 5120)]
 */
class SecureFileUpload implements ValidationRule
{
    /**
     * Predefined file type presets with their allowed extensions, MIME types, and magic bytes
     */
    protected const PRESETS = [
        'images' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'],
            'mimes' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/bmp',
                'image/svg+xml',
            ],
            'magic_bytes' => [
                'jpg' => [[0xFF, 0xD8, 0xFF]],
                'jpeg' => [[0xFF, 0xD8, 0xFF]],
                'png' => [[0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A]],
                'gif' => [[0x47, 0x49, 0x46, 0x38, 0x37, 0x61], [0x47, 0x49, 0x46, 0x38, 0x39, 0x61]], // GIF87a, GIF89a
                'webp' => [[0x52, 0x49, 0x46, 0x46]], // RIFF header (WebP starts with RIFF)
                'bmp' => [[0x42, 0x4D]], // BM
                'svg' => null, // SVG is text-based, validated differently
            ],
        ],
        'documents' => [
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'txt', 'rtf', 'csv'],
            'mimes' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.presentation',
                'text/plain',
                'text/rtf',
                'application/rtf',
                'text/csv',
                'application/csv',
            ],
            'magic_bytes' => [
                'pdf' => [[0x25, 0x50, 0x44, 0x46]], // %PDF
                'doc' => [[0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1]], // OLE Compound Document
                'docx' => [[0x50, 0x4B, 0x03, 0x04]], // ZIP (Office Open XML)
                'xls' => [[0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1]], // OLE Compound Document
                'xlsx' => [[0x50, 0x4B, 0x03, 0x04]], // ZIP
                'ppt' => [[0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1]], // OLE Compound Document
                'pptx' => [[0x50, 0x4B, 0x03, 0x04]], // ZIP
                'odt' => [[0x50, 0x4B, 0x03, 0x04]], // ZIP (OpenDocument)
                'ods' => [[0x50, 0x4B, 0x03, 0x04]], // ZIP
                'odp' => [[0x50, 0x4B, 0x03, 0x04]], // ZIP
                'txt' => null, // Text files don't have magic bytes
                'rtf' => [[0x7B, 0x5C, 0x72, 0x74, 0x66]], // {\rtf
                'csv' => null, // CSV files don't have magic bytes
            ],
        ],
        'archives' => [
            'extensions' => ['zip', 'rar', '7z', 'tar', 'gz'],
            'mimes' => [
                'application/zip',
                'application/x-zip-compressed',
                'application/x-rar-compressed',
                'application/vnd.rar',
                'application/x-7z-compressed',
                'application/x-tar',
                'application/gzip',
                'application/x-gzip',
            ],
            'magic_bytes' => [
                'zip' => [[0x50, 0x4B, 0x03, 0x04], [0x50, 0x4B, 0x05, 0x06], [0x50, 0x4B, 0x07, 0x08]],
                'rar' => [[0x52, 0x61, 0x72, 0x21, 0x1A, 0x07]], // Rar!
                '7z' => [[0x37, 0x7A, 0xBC, 0xAF, 0x27, 0x1C]], // 7z
                'tar' => [[0x75, 0x73, 0x74, 0x61, 0x72]], // ustar (at offset 257)
                'gz' => [[0x1F, 0x8B]], // Gzip
            ],
        ],
        'audio' => [
            'extensions' => ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'],
            'mimes' => [
                'audio/mpeg',
                'audio/mp3',
                'audio/wav',
                'audio/x-wav',
                'audio/ogg',
                'audio/flac',
                'audio/aac',
                'audio/mp4',
                'audio/x-m4a',
            ],
            'magic_bytes' => [
                'mp3' => [[0xFF, 0xFB], [0xFF, 0xFA], [0xFF, 0xF3], [0xFF, 0xF2], [0x49, 0x44, 0x33]], // MP3 frame sync or ID3
                'wav' => [[0x52, 0x49, 0x46, 0x46]], // RIFF
                'ogg' => [[0x4F, 0x67, 0x67, 0x53]], // OggS
                'flac' => [[0x66, 0x4C, 0x61, 0x43]], // fLaC
                'aac' => [[0xFF, 0xF1], [0xFF, 0xF9]], // AAC ADTS
                'm4a' => [[0x00, 0x00, 0x00]], // ftyp (MP4 container, needs special handling)
            ],
        ],
        'video' => [
            'extensions' => ['mp4', 'webm', 'avi', 'mov', 'mkv', 'wmv'],
            'mimes' => [
                'video/mp4',
                'video/webm',
                'video/x-msvideo',
                'video/quicktime',
                'video/x-matroska',
                'video/x-ms-wmv',
            ],
            'magic_bytes' => [
                'mp4' => [[0x00, 0x00, 0x00]], // ftyp (needs offset check)
                'webm' => [[0x1A, 0x45, 0xDF, 0xA3]], // EBML
                'avi' => [[0x52, 0x49, 0x46, 0x46]], // RIFF
                'mov' => [[0x00, 0x00, 0x00]], // ftyp
                'mkv' => [[0x1A, 0x45, 0xDF, 0xA3]], // EBML
                'wmv' => [[0x30, 0x26, 0xB2, 0x75, 0x8E, 0x66, 0xCF, 0x11]], // ASF header
            ],
        ],
    ];

    /**
     * Dangerous file extensions that should NEVER be allowed
     */
    protected const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'exe', 'dll', 'bat', 'cmd', 'com', 'msi', 'scr', 'vbs', 'vbe',
        'js', 'jse', 'ws', 'wsf', 'wsc', 'wsh',
        'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2',
        'sh', 'bash', 'zsh', 'csh', 'ksh',
        'pl', 'pm', 'py', 'pyc', 'pyo', 'pyw', 'rb', 'rbw',
        'asp', 'aspx', 'cer', 'csr', 'jsp', 'jspx',
        'htaccess', 'htpasswd', 'ini', 'config',
        'swf', 'jar', 'war', 'class',
        'cgi', 'fcgi',
        'hta', 'mht', 'mhtml',
        'svg', // SVG can contain JavaScript - only allow if explicitly needed
    ];

    /**
     * Dangerous MIME types
     */
    protected const DANGEROUS_MIMES = [
        'application/x-httpd-php',
        'application/x-php',
        'text/x-php',
        'application/x-executable',
        'application/x-msdownload',
        'application/x-msdos-program',
        'application/javascript',
        'text/javascript',
        'application/x-javascript',
        'text/html',
        'application/xhtml+xml',
    ];

    protected array $allowedExtensions = [];
    protected array $allowedMimes = [];
    protected array $magicBytes = [];
    protected int $maxSize; // in KB
    protected bool $allowSvg = false;

    /**
     * Create a new rule instance.
     *
     * @param string|array $types Preset name ('images', 'documents', 'all') or array of extensions
     * @param int $maxSize Maximum file size in KB (default: 10240 = 10MB)
     * @param bool $allowSvg Whether to allow SVG files (disabled by default due to XSS risks)
     */
    public function __construct(
        string|array $types = 'images',
        int $maxSize = 10240,
        bool $allowSvg = false
    ) {
        $this->maxSize = $maxSize;
        $this->allowSvg = $allowSvg;
        $this->initializeAllowedTypes($types);
    }

    /**
     * Initialize allowed types based on preset or custom array
     */
    protected function initializeAllowedTypes(string|array $types): void
    {
        if (is_string($types)) {
            if ($types === 'all') {
                // Combine all presets
                foreach (self::PRESETS as $preset) {
                    $this->allowedExtensions = array_merge($this->allowedExtensions, $preset['extensions']);
                    $this->allowedMimes = array_merge($this->allowedMimes, $preset['mimes']);
                    $this->magicBytes = array_merge($this->magicBytes, $preset['magic_bytes']);
                }
                $this->allowedExtensions = array_unique($this->allowedExtensions);
                $this->allowedMimes = array_unique($this->allowedMimes);
            } elseif (isset(self::PRESETS[$types])) {
                $preset = self::PRESETS[$types];
                $this->allowedExtensions = $preset['extensions'];
                $this->allowedMimes = $preset['mimes'];
                $this->magicBytes = $preset['magic_bytes'];
            } else {
                throw new \InvalidArgumentException("Unknown preset: {$types}. Available: images, documents, archives, audio, video, all");
            }
        } else {
            // Custom array of extensions
            $this->allowedExtensions = array_map('strtolower', $types);
            $this->buildMimesAndMagicBytes();
        }

        // Remove SVG if not explicitly allowed
        if (!$this->allowSvg) {
            $this->allowedExtensions = array_filter($this->allowedExtensions, fn($ext) => $ext !== 'svg');
            $this->allowedMimes = array_filter($this->allowedMimes, fn($mime) => $mime !== 'image/svg+xml');
            unset($this->magicBytes['svg']);
        }
    }

    /**
     * Build MIME types and magic bytes for custom extensions
     */
    protected function buildMimesAndMagicBytes(): void
    {
        $allMagicBytes = [];
        $allMimes = [];

        foreach (self::PRESETS as $preset) {
            $allMagicBytes = array_merge($allMagicBytes, $preset['magic_bytes']);
            foreach ($preset['extensions'] as $index => $ext) {
                if (!isset($allMimes[$ext])) {
                    $allMimes[$ext] = [];
                }
                // Map extension to its MIME types
                $allMimes[$ext][] = $preset['mimes'][$index] ?? null;
            }
        }

        foreach ($this->allowedExtensions as $ext) {
            if (isset($allMagicBytes[$ext])) {
                $this->magicBytes[$ext] = $allMagicBytes[$ext];
            }
        }

        // Build MIME types from extension mapping
        $this->allowedMimes = $this->getDefaultMimesForExtensions($this->allowedExtensions);
    }

    /**
     * Get default MIME types for given extensions
     */
    protected function getDefaultMimesForExtensions(array $extensions): array
    {
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'rtf' => 'application/rtf',
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar',
            '7z' => 'application/x-7z-compressed',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'avi' => 'video/x-msvideo',
        ];

        $mimes = [];
        foreach ($extensions as $ext) {
            if (isset($mimeMap[$ext])) {
                $mimes[] = $mimeMap[$ext];
            }
        }

        return array_unique($mimes);
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute The attribute name being validated
     * @param mixed $value The uploaded file
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ensure we have an uploaded file
        if (!$value instanceof UploadedFile) {
            $fail('validation.file')->translate();
            return;
        }

        // Check if file was uploaded successfully
        if (!$value->isValid()) {
            $fail('validation.uploaded')->translate();
            return;
        }

        $filename = $value->getClientOriginalName();
        $extension = strtolower($value->getClientOriginalExtension());
        $mimeType = $value->getMimeType();

        // 1. Check for null bytes in filename (null byte injection attack)
        if (str_contains($filename, "\0")) {
            $fail('validation.secure_file.null_byte')->translate();
            return;
        }

        // 2. Check for double extensions (e.g., file.php.jpg)
        if ($this->hasDoubleExtension($filename)) {
            $fail('validation.secure_file.double_extension')->translate();
            return;
        }

        // 3. Check for dangerous extensions anywhere in filename
        if ($this->containsDangerousExtension($filename)) {
            $fail('validation.secure_file.dangerous_extension')->translate();
            return;
        }

        // 4. Validate extension is in allowed list
        if (!in_array($extension, $this->allowedExtensions, true)) {
            $fail('validation.secure_file.invalid_extension')
                ->translate(['extensions' => implode(', ', $this->allowedExtensions)]);
            return;
        }

        // 5. Validate MIME type
        if (!in_array($mimeType, $this->allowedMimes, true)) {
            $fail('validation.secure_file.invalid_mime')->translate();
            return;
        }

        // 6. Validate MIME type is not dangerous
        if (in_array($mimeType, self::DANGEROUS_MIMES, true)) {
            $fail('validation.secure_file.dangerous_mime')->translate();
            return;
        }

        // 7. Validate magic bytes (file signature)
        if (!$this->validateMagicBytes($value, $extension)) {
            $fail('validation.secure_file.invalid_signature')->translate();
            return;
        }

        // 8. Check file size
        $fileSizeKb = $value->getSize() / 1024;
        if ($fileSizeKb > $this->maxSize) {
            $fail('validation.secure_file.max_size')
                ->translate(['max' => $this->formatFileSize($this->maxSize * 1024)]);
            return;
        }

        // 9. Additional SVG security check
        if ($extension === 'svg' && $this->allowSvg) {
            if (!$this->validateSvgSecurity($value)) {
                $fail('validation.secure_file.svg_unsafe')->translate();
                return;
            }
        }

        // 10. Verify MIME type matches extension (consistency check)
        if (!$this->mimeMatchesExtension($mimeType, $extension)) {
            $fail('validation.secure_file.mime_mismatch')->translate();
            return;
        }
    }

    /**
     * Check if filename has double extension
     */
    protected function hasDoubleExtension(string $filename): bool
    {
        // Remove the last extension
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

        // Check if remaining name has a dangerous extension
        $remainingExt = strtolower(pathinfo($nameWithoutExt, PATHINFO_EXTENSION));

        if (empty($remainingExt)) {
            return false;
        }

        // Check if the remaining extension is dangerous
        return in_array($remainingExt, self::DANGEROUS_EXTENSIONS, true);
    }

    /**
     * Check if filename contains any dangerous extension
     */
    protected function containsDangerousExtension(string $filename): bool
    {
        $lowerFilename = strtolower($filename);

        foreach (self::DANGEROUS_EXTENSIONS as $dangerousExt) {
            // Check for .ext anywhere in filename
            if (str_contains($lowerFilename, '.' . $dangerousExt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate file magic bytes (file signature)
     */
    protected function validateMagicBytes(UploadedFile $file, string $extension): bool
    {
        // If no magic bytes defined for this extension, skip this check
        if (!isset($this->magicBytes[$extension]) || $this->magicBytes[$extension] === null) {
            return true;
        }

        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return false;
        }

        // Read first 16 bytes (enough for most signatures)
        $header = fread($handle, 16);
        fclose($handle);

        if ($header === false) {
            return false;
        }

        $headerBytes = array_values(unpack('C*', $header));

        foreach ($this->magicBytes[$extension] as $signature) {
            if ($this->matchesSignature($headerBytes, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if header bytes match a signature
     */
    protected function matchesSignature(array $headerBytes, array $signature): bool
    {
        if (count($headerBytes) < count($signature)) {
            return false;
        }

        for ($i = 0; $i < count($signature); $i++) {
            if ($headerBytes[$i] !== $signature[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate SVG file for security issues
     */
    protected function validateSvgSecurity(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getRealPath());

        if ($content === false) {
            return false;
        }

        // Check for potentially dangerous content in SVG
        $dangerousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i', // onclick, onload, etc.
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<foreignObject/i',
            '/data:/i', // data URIs can be dangerous
            '/<use.*href/i', // External references
            '/xlink:href\s*=\s*["\'](?!#)/i', // External xlink references
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }

        // Verify it's valid XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);
        libxml_clear_errors();

        if ($xml === false) {
            return false;
        }

        // Check root element is SVG
        if (strtolower($xml->getName()) !== 'svg') {
            return false;
        }

        return true;
    }

    /**
     * Check if MIME type is consistent with extension
     */
    protected function mimeMatchesExtension(string $mimeType, string $extension): bool
    {
        $expectedMimes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'bmp' => ['image/bmp', 'image/x-ms-bmp'],
            'svg' => ['image/svg+xml'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
            'ppt' => ['application/vnd.ms-powerpoint'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
            'odt' => ['application/vnd.oasis.opendocument.text', 'application/zip'],
            'ods' => ['application/vnd.oasis.opendocument.spreadsheet', 'application/zip'],
            'odp' => ['application/vnd.oasis.opendocument.presentation', 'application/zip'],
            'txt' => ['text/plain'],
            'rtf' => ['text/rtf', 'application/rtf'],
            'csv' => ['text/csv', 'text/plain', 'application/csv'],
            'zip' => ['application/zip', 'application/x-zip-compressed'],
            'rar' => ['application/x-rar-compressed', 'application/vnd.rar'],
            '7z' => ['application/x-7z-compressed'],
            'tar' => ['application/x-tar'],
            'gz' => ['application/gzip', 'application/x-gzip'],
            'mp3' => ['audio/mpeg', 'audio/mp3'],
            'wav' => ['audio/wav', 'audio/x-wav'],
            'ogg' => ['audio/ogg', 'application/ogg'],
            'flac' => ['audio/flac', 'audio/x-flac'],
            'aac' => ['audio/aac', 'audio/x-aac'],
            'm4a' => ['audio/mp4', 'audio/x-m4a'],
            'mp4' => ['video/mp4'],
            'webm' => ['video/webm'],
            'avi' => ['video/x-msvideo', 'video/avi'],
            'mov' => ['video/quicktime'],
            'mkv' => ['video/x-matroska'],
            'wmv' => ['video/x-ms-wmv'],
        ];

        // If extension not in map, allow any MIME from allowed list
        if (!isset($expectedMimes[$extension])) {
            return in_array($mimeType, $this->allowedMimes, true);
        }

        return in_array($mimeType, $expectedMimes[$extension], true);
    }

    /**
     * Format file size for display
     */
    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Static factory methods for common use cases
     */
    public static function images(int $maxSize = 5120, bool $allowSvg = false): self
    {
        return new self('images', $maxSize, $allowSvg);
    }

    public static function documents(int $maxSize = 10240): self
    {
        return new self('documents', $maxSize);
    }

    public static function pdf(int $maxSize = 10240): self
    {
        return new self(['pdf'], $maxSize);
    }

    public static function archives(int $maxSize = 51200): self
    {
        return new self('archives', $maxSize);
    }

    public static function audio(int $maxSize = 20480): self
    {
        return new self('audio', $maxSize);
    }

    public static function video(int $maxSize = 102400): self
    {
        return new self('video', $maxSize);
    }

    public static function all(int $maxSize = 10240): self
    {
        return new self('all', $maxSize);
    }
}

