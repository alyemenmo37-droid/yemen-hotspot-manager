<?php
/**
 * YHM - Database (JSON-based)
 * طبقة الوصول للبيانات
 */

class Database
{
    private array $data = [];
    private bool $loaded = false;
    
    public function __construct(
        private string $file,
    ) {}
    
    /**
     * تحميل البيانات من الملف
     */
    private function load(): void
    {
        if ($this->loaded) {
            return;
        }
        
        if (file_exists($this->file)) {
            $content = file_get_contents($this->file);
            $this->data = json_decode($content, true) ?: [];
        } else {
            $this->data = [];
        }
        
        $this->loaded = true;
    }
    
    /**
     * حفظ البيانات للملف
     */
    public function save(): void
    {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents(
            $this->file,
            json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
    
    /**
     * الحصول على كل السجلات
     */
    public function all(): array
    {
        $this->load();
        return $this->data;
    }
    
    /**
     * البحث بكولوم وقيمة
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $this->load();
        
        foreach ($this->data as $row) {
            if (($row[$column] ?? null) === $value) {
                return $row;
            }
        }
        
        return null;
    }
    
    /**
     * إضافة سجل
     */
    public function insert(array $row): void
    {
        $this->load();
        $this->data[] = $row;
        $this->save();
    }
    
    /**
     * تحديث سجل
     */
    public function update(string $column, mixed $value, array $changes): bool
    {
        $this->load();
        $updated = false;
        
        foreach ($this->data as &$row) {
            if (($row[$column] ?? null) === $value) {
                $row = array_merge($row, $changes);
                $updated = true;
                break;
            }
        }
        unset($row);
        
        if ($updated) {
            $this->save();
        }
        
        return $updated;
    }
    
    /**
     * حذف سجل
     */
    public function delete(string $column, mixed $value): bool
    {
        $this->load();
        $initial = count($this->data);
        
        $this->data = array_values(array_filter(
            $this->data,
            fn($row) => ($row[$column] ?? null) !== $value
        ));
        
        if (count($this->data) < $initial) {
            $this->save();
            return true;
        }
        
        return false;
    }
    
    /**
     * فلترة
     */
    public function filter(callable $callback): array
    {
        $this->load();
        return array_values(array_filter($this->data, $callback));
    }
    
    /**
     * عدد السجلات
     */
    public function count(): int
    {
        $this->load();
        return count($this->data);
    }
}
