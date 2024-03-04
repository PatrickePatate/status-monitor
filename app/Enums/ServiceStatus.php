<?php
namespace App\Enums;

enum ServiceStatus: string {
    case AVAILABLE = "AVAILABLE";
    case PARTIAL = "PARTIAL";
    case OUTAGE = "OUTAGE";
    case MAINTENANCE = "MAINTENANCE";

    // Fulfills the interface contract.
    public function label(): string
    {
        return match($this) {
            ServiceStatus::AVAILABLE => __('enum_status.AVAILABLE'),
            ServiceStatus::PARTIAL => __('enum_status.PARTIAL'),
            ServiceStatus::OUTAGE => __('enum_status.OUTAGE'),
            ServiceStatus::MAINTENANCE => __('enum_status.MAINTENANCE'),
        };
    }

    public function color(): string
    {
        return match($this) {
            ServiceStatus::AVAILABLE => 'success',
            ServiceStatus::PARTIAL => 'warning',
            ServiceStatus::OUTAGE => 'danger',
            ServiceStatus::MAINTENANCE => 'secondary',
        };
    }
}
