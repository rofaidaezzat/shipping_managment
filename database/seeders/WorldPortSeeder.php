<?php

namespace Database\Seeders;

use App\Models\MasterPort;
use Illuminate\Database\Seeder;

class WorldPortSeeder extends Seeder
{
    /**
     * Standard world ports reference catalog grouped by country.
     */
    public const PORTS_BY_COUNTRY = [
        'Egypt' => [
            ['name' => 'Alexandria Port',        'code' => 'ALX'],
            ['name' => 'Alexandria Hub',         'code' => 'ALX'],
            ['name' => 'Port Said Port',         'code' => 'PSD'],
            ['name' => 'Port Said East',         'code' => 'PSE'],
            ['name' => 'Damietta Port',          'code' => 'DAM'],
            ['name' => 'Sokhna Port',            'code' => 'SKH'],
            ['name' => 'Asyut Dry Port',         'code' => 'ASY'],
            ['name' => 'Suez Port',              'code' => 'SUZ'],
            ['name' => 'Dekheila Port',          'code' => 'DKH'],
            ['name' => 'Safaga Port',            'code' => 'SFG'],
            ['name' => 'Adabiya Port',           'code' => 'ADB'],
        ],
        'Saudi Arabia' => [
            ['name' => 'Jeddah Islamic Port',    'code' => 'JED'],
            ['name' => 'King Abdulaziz Port',    'code' => 'DAM'],
            ['name' => 'Jubail Industrial Port', 'code' => 'JUB'],
            ['name' => 'Yanbu Port',             'code' => 'YNB'],
            ['name' => 'Jizan Port',             'code' => 'GIZ'],
        ],
        'United Arab Emirates' => [
            ['name' => 'Jebel Ali Port',         'code' => 'DXB'],
            ['name' => 'Dubai Port',             'code' => 'DXB'],
            ['name' => 'Abu Dhabi Port',         'code' => 'AUH'],
            ['name' => 'Khalifa Port',           'code' => 'KHF'],
            ['name' => 'Sharjah Port',           'code' => 'SHJ'],
        ],
        'Lebanon' => [
            ['name' => 'Beirut Port',            'code' => 'BEY'],
            ['name' => 'Tripoli Port',           'code' => 'TRI'],
            ['name' => 'Sidon Port',             'code' => 'SID'],
        ],
        'Jordan' => [
            ['name' => 'Aqaba Port',             'code' => 'AQJ'],
        ],
        'Kuwait' => [
            ['name' => 'Shuwaikh Port',          'code' => 'KWI'],
            ['name' => 'Shuaiba Port',           'code' => 'SHU'],
        ],
        'Oman' => [
            ['name' => 'Sohar Port',             'code' => 'SOH'],
            ['name' => 'Salalah Port',           'code' => 'SAL'],
            ['name' => 'Muscat Port',            'code' => 'MCT'],
        ],
        'Qatar' => [
            ['name' => 'Hamad Port',             'code' => 'HMD'],
            ['name' => 'Doha Port',              'code' => 'DOH'],
        ],
        'Bahrain' => [
            ['name' => 'Khalifa Bin Salman Port','code' => 'KBS'],
        ],
        'Netherlands' => [
            ['name' => 'Rotterdam Port',         'code' => 'RTM'],
            ['name' => 'Amsterdam Port',         'code' => 'AMS'],
        ],
        'Belgium' => [
            ['name' => 'Antwerp Terminal',       'code' => 'ANR'],
            ['name' => 'Antwerp North Sea Port', 'code' => 'ANS'],
            ['name' => 'Ghent Port',             'code' => 'GNT'],
            ['name' => 'Bruges Port',            'code' => 'ZBR'],
        ],
        'Germany' => [
            ['name' => 'Hamburg Port',           'code' => 'HAM'],
            ['name' => 'Bremen Port',            'code' => 'BRE'],
            ['name' => 'Bremerhaven Port',       'code' => 'BHV'],
            ['name' => 'Duisburg Inland Port',   'code' => 'DUI'],
        ],
        'Spain' => [
            ['name' => 'Valencia Port',          'code' => 'VLC'],
            ['name' => 'Barcelona Port',         'code' => 'BCN'],
            ['name' => 'Algeciras Port',         'code' => 'ALG'],
            ['name' => 'Bilbao Port',            'code' => 'BIO'],
        ],
        'France' => [
            ['name' => 'Marseille Port',         'code' => 'MRS'],
            ['name' => 'Le Havre Port',          'code' => 'LEH'],
            ['name' => 'Dunkirk Port',           'code' => 'DUK'],
            ['name' => 'Nantes Port',            'code' => 'NTE'],
        ],
        'Italy' => [
            ['name' => 'Genoa Port',             'code' => 'GOA'],
            ['name' => 'La Spezia Port',         'code' => 'SPE'],
            ['name' => 'Naples Port',            'code' => 'NAP'],
            ['name' => 'Gioia Tauro Port',       'code' => 'GIT'],
            ['name' => 'Venice Port',            'code' => 'VCE'],
            ['name' => 'Trieste Port',           'code' => 'TRS'],
            ['name' => 'Livorno Port',           'code' => 'LIV'],
        ],
        'United Kingdom' => [
            ['name' => 'Felixstowe Port',        'code' => 'FXT'],
            ['name' => 'Southampton Port',       'code' => 'SOU'],
            ['name' => 'London Gateway Port',    'code' => 'LGP'],
            ['name' => 'Liverpool Port',         'code' => 'LIV'],
            ['name' => 'Bristol Port',           'code' => 'BRS'],
        ],
        'Greece' => [
            ['name' => 'Piraeus Port',           'code' => 'PIR'],
            ['name' => 'Thessaloniki Port',      'code' => 'SKG'],
            ['name' => 'Patras Port',            'code' => 'PAT'],
        ],
        'Turkey' => [
            ['name' => 'Mersin Port',            'code' => 'MRS'],
            ['name' => 'Istanbul Port',          'code' => 'IST'],
            ['name' => 'Izmir Port',             'code' => 'ADB'],
            ['name' => 'Ambarli Port',           'code' => 'AMB'],
        ],
        'Morocco' => [
            ['name' => 'Tanger Med Port',        'code' => 'TNG'],
            ['name' => 'Casablanca Port',        'code' => 'CAS'],
            ['name' => 'Agadir Port',            'code' => 'AGA'],
        ],
        'China' => [
            ['name' => 'Shanghai Port',          'code' => 'SHA'],
            ['name' => 'Shenzhen Port',          'code' => 'SZX'],
            ['name' => 'Ningbo Port',            'code' => 'NGB'],
            ['name' => 'Guangzhou Port',         'code' => 'CAN'],
            ['name' => 'Tianjin Port',           'code' => 'TSN'],
            ['name' => 'Qingdao Port',           'code' => 'TAO'],
            ['name' => 'Xiamen Port',            'code' => 'XMN'],
            ['name' => 'Dalian Port',            'code' => 'DLC'],
            ['name' => 'Hong Kong Port',         'code' => 'HKG'],
        ],
        'United States' => [
            ['name' => 'Los Angeles Port',       'code' => 'LAX'],
            ['name' => 'Long Beach Port',        'code' => 'LGB'],
            ['name' => 'New York Port',          'code' => 'NYC'],
            ['name' => 'Savannah Port',          'code' => 'SAV'],
            ['name' => 'Houston Port',           'code' => 'HOU'],
            ['name' => 'Seattle Port',           'code' => 'SEA'],
            ['name' => 'Miami Port',             'code' => 'MIA'],
            ['name' => 'Baltimore Port',         'code' => 'BAL'],
        ],
        'Singapore' => [
            ['name' => 'Singapore Port',         'code' => 'SIN'],
            ['name' => 'Tuas Port',              'code' => 'TUP'],
            ['name' => 'Jurong Port',            'code' => 'JRP'],
        ],
        'South Korea' => [
            ['name' => 'Busan Port',             'code' => 'PUS'],
            ['name' => 'Incheon Port',           'code' => 'ICN'],
            ['name' => 'Gwangyang Port',         'code' => 'KWJ'],
        ],
        'Japan' => [
            ['name' => 'Tokyo Port',             'code' => 'TYO'],
            ['name' => 'Yokohama Port',          'code' => 'YOK'],
            ['name' => 'Osaka Port',             'code' => 'OSA'],
            ['name' => 'Kobe Port',              'code' => 'UKB'],
            ['name' => 'Nagoya Port',            'code' => 'NGO'],
        ],
        'India' => [
            ['name' => 'Jawaharlal Nehru Port',  'code' => 'JNH'],
            ['name' => 'Mumbai Port',            'code' => 'BOM'],
            ['name' => 'Chennai Port',           'code' => 'MAA'],
            ['name' => 'Mundra Port',            'code' => 'MUN'],
            ['name' => 'Kolkata Port',           'code' => 'CCU'],
            ['name' => 'Cochin Port',            'code' => 'COK'],
            ['name' => 'Visakhapatnam Port',     'code' => 'VTZ'],
        ],
        'South Africa' => [
            ['name' => 'Durban Port',            'code' => 'DUR'],
            ['name' => 'Cape Town Port',         'code' => 'CPT'],
            ['name' => 'Port Elizabeth Port',    'code' => 'PLZ'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::PORTS_BY_COUNTRY as $country => $ports) {
            foreach ($ports as $port) {
                MasterPort::firstOrCreate(
                    [
                        'country' => $country,
                        'name'    => $port['name'],
                        'code'    => $port['code'],
                    ],
                    [
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
