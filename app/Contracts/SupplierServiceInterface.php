<?php

namespace App\Contracts;

use App\Models\Supplier;

interface SupplierServiceInterface
{
    /**
     * Export a single supplier and its layups to an array layout ready for JSON encoding.
     */
    public function export(Supplier $supplier): string;

    /**
     * Export all suppliers and their layups to an array layout ready for JSON encoding.
     */
    public function exportAll(): string;

    /**
     * Import suppliers from an array of decoded JSON data.
     */
    public function import(Supplier $supplier, array $data, string $strategy, array $resolutionMap, bool $isDryRun): array;
}
