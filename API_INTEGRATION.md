# Integrasi API Eksternal Agenda

Dokumentasi untuk integrasi API agenda dari `https://backend.samagov.id/api/ppid/agenda`

## Konfigurasi

Tambahkan konfigurasi berikut di file `.env` (opsional, default sudah diset):

```env
EXTERNAL_AGENDA_API_URL=https://backend.samagov.id/api/ppid/agenda
```

## Endpoint yang Tersedia

### 1. Fetch Agenda Eksternal
**GET** `/api/external/agendas`

Mengambil semua agenda dari API eksternal.

**Query Parameters:**
- `year` (opsional): Filter berdasarkan tahun
- `month` (opsional): Filter berdasarkan bulan
- `date` (opsional): Filter berdasarkan tanggal (format: Y-m-d)

**Contoh:**
```
GET /api/external/agendas
GET /api/external/agendas?year=2025&month=11
GET /api/external/agendas?date=2025-11-19
```

**Response:**
```json
{
    "success": true,
    "message": "Data agenda eksternal berhasil diambil",
    "data": [...],
    "count": 10
}
```

### 2. Get External Agendas by Date
**GET** `/api/external/agendas/date`

Mengambil agenda eksternal berdasarkan tanggal tertentu.

**Request Body:**
```json
{
    "date": "2025-11-19"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Agenda eksternal berhasil diambil.",
    "data": [...]
}
```

### 3. Get Merged Agendas (Local + External)
**GET** `/api/merged/agendas`

Menggabungkan agenda lokal dengan agenda eksternal.

**Query Parameters:**
- `year` (opsional, default: tahun saat ini)
- `month` (opsional, default: bulan saat ini)
- `date` (opsional): Filter berdasarkan tanggal spesifik

**Contoh:**
```
GET /api/merged/agendas?year=2025&month=11
GET /api/merged/agendas?date=2025-11-19
```

**Response:**
```json
{
    "success": true,
    "data": [...],
    "count": 25,
    "local_count": 15,
    "external_count": 10
}
```

## Mapping Data

Data dari API eksternal akan ditransformasi ke format lokal:

| API Eksternal | Format Lokal |
|--------------|--------------|
| `judul` | `agenda_name` |
| `perihal` | `description` |
| `waktu_awal` | `date` |
| `waktu_akhir` | `end_time` |
| `tempat` | `location` |
| `dihadiri_oleh` | `involved_institution` |

## Service Class

Service class `ExternalAgendaService` menyediakan method berikut:

- `fetchAgendas(array $params = [])`: Fetch semua agenda
- `getAgendasByDate(string $date)`: Get agenda berdasarkan tanggal
- `getAgendasByMonth(int $year, int $month)`: Get agenda berdasarkan bulan
- `getAgendasByDateRange(string $startDate, string $endDate)`: Get agenda berdasarkan range tanggal
- `transformToLocalFormat(array $externalAgenda)`: Transform data ke format lokal

## Catatan

- Semua agenda eksternal otomatis dianggap sebagai `approved` dan `public`
- Data eksternal tidak disimpan ke database lokal (hanya di-fetch saat diperlukan)
- Jika API eksternal tidak dapat diakses, akan mengembalikan `null` dan error akan di-log

## Penggunaan di Frontend

Contoh penggunaan dengan JavaScript/AJAX:

```javascript
// Fetch agenda eksternal
fetch('/api/external/agendas?year=2025&month=11')
    .then(response => response.json())
    .then(data => {
        console.log('External agendas:', data.data);
    });

// Fetch merged agendas
fetch('/api/merged/agendas?year=2025&month=11')
    .then(response => response.json())
    .then(data => {
        console.log('Total agendas:', data.count);
        console.log('Local:', data.local_count);
        console.log('External:', data.external_count);
    });
```

