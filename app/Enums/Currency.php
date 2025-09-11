<?php

namespace App\Enums;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CHF = 'CHF';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case CNY = 'CNY';
    case HKD = 'HKD';
    case SGD = 'SGD';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case PLN = 'PLN';
    case CZK = 'CZK';
    case HUF = 'HUF';
    case RUB = 'RUB';
    case INR = 'INR';
    case BRL = 'BRL';
    case ZAR = 'ZAR';
    case KRW = 'KRW';
    case MXN = 'MXN';
    case TRY = 'TRY';
    case THB = 'THB';
    case MYR = 'MYR';
    case IDR = 'IDR';
    case PHP = 'PHP';
    case VND = 'VND';
    case ILS = 'ILS';
    case AED = 'AED';
    case SAR = 'SAR';
    case EGP = 'EGP';
    case QAR = 'QAR';
    case KWD = 'KWD';
    case BHD = 'BHD';
    case OMR = 'OMR';
    case JOD = 'JOD';
    case LBP = 'LBP';
    case PKR = 'PKR';
    case LKR = 'LKR';
    case BGN = 'BGN';
    case RON = 'RON';
    case HRK = 'HRK';
    case RSD = 'RSD';
    case UAH = 'UAH';
    case KZT = 'KZT';
    case UZS = 'UZS';
    case GEL = 'GEL';
    case AMD = 'AMD';
    case AZN = 'AZN';

    /** Get the currency symbol */
    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::GBP => '£',
            self::JPY => '¥',
            self::CHF => 'CHF',
            self::CAD => 'C$',
            self::AUD => 'A$',
            self::CNY => '¥',
            self::HKD => 'HK$',
            self::SGD => 'S$',
            self::SEK => 'kr',
            self::NOK => 'kr',
            self::DKK => 'kr',
            self::PLN => 'zł',
            self::CZK => 'Kč',
            self::HUF => 'Ft',
            self::RUB => '₽',
            self::INR => '₹',
            self::BRL => 'R$',
            self::ZAR => 'R',
            self::KRW => '₩',
            self::MXN => '$',
            self::TRY => '₺',
            self::THB => '฿',
            self::MYR => 'RM',
            self::IDR => 'Rp',
            self::PHP => '₱',
            self::VND => '₫',
            self::ILS => '₪',
            self::AED => 'د.إ',
            self::SAR => '﷼',
            self::EGP => '£',
            self::QAR => '﷼',
            self::KWD => 'د.ك',
            self::BHD => '.د.ب',
            self::OMR => '﷼',
            self::JOD => 'د.ا',
            self::LBP => '£',
            self::PKR => '₨',
            self::LKR => '₨',
            self::BGN => 'лв',
            self::RON => 'lei',
            self::HRK => 'kn',
            self::RSD => 'дин',
            self::UAH => '₴',
            self::KZT => '₸',
            self::UZS => 'лв',
            self::GEL => '₾',
            self::AMD => '֏',
            self::AZN => '₼',
        };
    }

    /** Get the currency display name */
    public function name(): string
    {
        return match ($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::GBP => 'British Pound',
            self::JPY => 'Japanese Yen',
            self::CHF => 'Swiss Franc',
            self::CAD => 'Canadian Dollar',
            self::AUD => 'Australian Dollar',
            self::CNY => 'Chinese Yuan',
            self::HKD => 'Hong Kong Dollar',
            self::SGD => 'Singapore Dollar',
            self::SEK => 'Swedish Krona',
            self::NOK => 'Norwegian Krone',
            self::DKK => 'Danish Krone',
            self::PLN => 'Polish Złoty',
            self::CZK => 'Czech Koruna',
            self::HUF => 'Hungarian Forint',
            self::RUB => 'Russian Ruble',
            self::INR => 'Indian Rupee',
            self::BRL => 'Brazilian Real',
            self::ZAR => 'South African Rand',
            self::KRW => 'South Korean Won',
            self::MXN => 'Mexican Peso',
            self::TRY => 'Turkish Lira',
            self::THB => 'Thai Baht',
            self::MYR => 'Malaysian Ringgit',
            self::IDR => 'Indonesian Rupiah',
            self::PHP => 'Philippine Peso',
            self::VND => 'Vietnamese Dong',
            self::ILS => 'Israeli Shekel',
            self::AED => 'UAE Dirham',
            self::SAR => 'Saudi Riyal',
            self::EGP => 'Egyptian Pound',
            self::QAR => 'Qatari Riyal',
            self::KWD => 'Kuwaiti Dinar',
            self::BHD => 'Bahraini Dinar',
            self::OMR => 'Omani Rial',
            self::JOD => 'Jordanian Dinar',
            self::LBP => 'Lebanese Pound',
            self::PKR => 'Pakistani Rupee',
            self::LKR => 'Sri Lankan Rupee',
            self::BGN => 'Bulgarian Lev',
            self::RON => 'Romanian Leu',
            self::HRK => 'Croatian Kuna',
            self::RSD => 'Serbian Dinar',
            self::UAH => 'Ukrainian Hryvnia',
            self::KZT => 'Kazakhstani Tenge',
            self::UZS => 'Uzbekistani Som',
            self::GEL => 'Georgian Lari',
            self::AMD => 'Armenian Dram',
            self::AZN => 'Azerbaijani Manat',
        };
    }

    /** Get display text with symbol and code */
    public function display(): string
    {
        return $this->symbol().' ('.$this->value.')';
    }

    /** Get the most common currencies for dropdowns */
    public static function common(): array
    {
        return [
            self::USD,
            self::EUR,
            self::GBP,
            self::JPY,
            self::CAD,
            self::AUD,
            self::CHF,
            self::CNY,
            self::SGD,
            self::HKD,
        ];
    }

    /** Get all currencies as an array for forms */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $currency) {
            $options[$currency->value] = $currency->display();
        }

        return $options;
    }

    /** Get common currencies as an array for forms */
    public static function commonOptions(): array
    {
        $options = [];
        foreach (self::common() as $currency) {
            $options[$currency->value] = $currency->display();
        }

        return $options;
    }
}
