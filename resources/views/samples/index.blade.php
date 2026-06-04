<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KÖKSAN CRM - Numune Modülü (Polimorfik Test)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <h3 class="mb-4 text-secondary">Numune Yönetimi (Polymorphic Engine)</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark font-weight-bold">
                        <h5 class="mb-0">Yeni Numune Gönder</h5>
                    </div>
                    <div class="card-body">
                        <form id="sampleForm">

                            <div class="mb-3 p-3 border rounded bg-white border-info">
                                <label class="form-label text-info fw-bold">1. Alıcı Tipi (Polymorphic)</label>
                                <select id="receivable_type" name="receivable_type" class="form-select" required>
                                    <option value="">Tip Seçin...</option>
                                    <option value="App\Models\Customer">Kurumsal Müşteri</option>
                                    <option value="App\Models\Person">Bireysel Şahıs</option>
                                </select>

                                <div class="mt-3">
                                    <label class="form-label text-info fw-bold">2. Alıcı Seçin</label>
                                    <select id="receivable_id" name="receivable_id" class="form-select" required
                                        disabled>
                                        <option value="">Önce tip seçiniz...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konu</label>
                                <input type="text" name="subject" class="form-control"
                                    placeholder="Örn: 500gr PET Numunesi" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Statü</label>
                                <select name="status" class="form-select" required>
                                    <option value="hazirlaniyor">Hazırlanıyor</option>
                                    <option value="kargoda">Kargoda</option>
                                    <option value="teslim_edildi">Teslim Edildi</option>
                                </select>
                            </div>

                            <div id="dynamicFieldsContainer" class="p-3 mb-3 bg-light border rounded">
                                <small class="text-muted d-block mb-2">Dinamik Alanlar (EAV Motoru)</small>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 fw-bold">Numuneyi Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Gönderim Geçmişi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Konu</th>
                                    <th>Alıcı Tipi & Adı</th>
                                    <th>Statü</th>
                                    <th>Kargo Firması</th>
                                </tr>
                            </thead>
                            <tbody id="samplesTableBody">
                                <tr>
                                    <td colspan="4" class="text-center">Yükleniyor...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_HEADERS = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        // DOM Yüklendiğinde
        document.addEventListener('DOMContentLoaded', () => {
            loadDynamicForm();
            loadSamples();
        });

        // POLİMORFİK UI: Alıcı Tipi değiştiğinde ID listesini API'den çek
        document.getElementById('receivable_type').addEventListener('change', async (e) => {
            const type = e.target.value;
            const idSelect = document.getElementById('receivable_id');

            idSelect.innerHTML = '<option value="">Yükleniyor...</option>';
            idSelect.disabled = true;

            if (!type) {
                idSelect.innerHTML = '<option value="">Önce tip seçiniz...</option>';
                return;
            }

            // Tipe göre gideceğimiz endpoint'i belirliyoruz
            const endpoint = type === 'App\\Models\\Customer' ? '/api/customers' : '/api/people';

            try {
                const response = await fetch(endpoint, {
                    headers: API_HEADERS
                });
                const result = await response.json();

                idSelect.innerHTML = '<option value="">Seçiniz...</option>';

                if (result.data && result.data.length > 0) {
                    result.data.forEach(item => {
                        // Müşteri ise company_name, Şahıs ise first_name last_name
                        const displayName = type === 'App\\Models\\Customer' ?
                            item.company_name :
                            `${item.first_name} ${item.last_name}`;

                        idSelect.innerHTML += `<option value="${item.id}">${displayName}</option>`;
                    });
                    idSelect.disabled = false;
                } else {
                    idSelect.innerHTML = '<option value="">Kayıt bulunamadı!</option>';
                }
            } catch (error) {
                console.error('Veri çekme hatası:', error);
                idSelect.innerHTML = '<option value="">Hata oluştu!</option>';
            }
        });

        // EAV MOTORU: Dinamik form çizimi
        async function loadDynamicForm() {
            const modelType = encodeURIComponent('App\\Models\\Sample');
            const response = await fetch(`/api/dynamic-fields?model_type=${modelType}`, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const container = document.getElementById('dynamicFieldsContainer');

            container.innerHTML = '<small class="text-muted d-block mb-2">Sistemden Gelen Alanlar</small>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(field => {
                    let inputHtml = '';
                    if (field.type === 'select') {
                        let options = field.options.map(opt => `<option value="${opt}">${opt}</option>`).join(
                            '');
                        inputHtml = `<select name="dynamic_${field.name}" class="form-select" ${field.is_required ? 'required' : ''}>
                                    <option value="">Seçiniz...</option>${options}
                                 </select>`;
                    } else {
                        inputHtml =
                            `<input type="${field.type}" name="dynamic_${field.name}" class="form-control" ${field.is_required ? 'required' : ''}>`;
                    }
                    container.innerHTML +=
                        `<div class="mb-2"><label class="form-label">${field.label}</label> ${inputHtml}</div>`;
                });
            }
        }

        // LİSTELEME: Numuneleri tabloya bas
        async function loadSamples() {
            const response = await fetch('/api/samples', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('samplesTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Kayıt bulunamadı.</td></tr>';
                return;
            }

            result.data.forEach(sample => {
                // Polimorfik Tipi okunabilir hale getir
                const typeBadge = sample.receiver.type === 'App\\Models\\Customer' ?
                    '<span class="badge bg-primary">Müşteri</span>' :
                    '<span class="badge bg-success">Şahıs</span>';

                // Dinamik EAV Verisi
                const cargo = sample.custom_data?.shipping_company || '-';

                tbody.innerHTML += `
                <tr>
                    <td class="fw-bold">${sample.subject}</td>
                    <td>${typeBadge} <br> <small>${sample.receiver.display_name}</small></td>
                    <td><span class="badge bg-secondary">${sample.status}</span></td>
                    <td><span class="text-info fw-bold">${cargo}</span></td>
                </tr>
            `;
            });
        }

        // KAYIT: Formu gönder
        document.getElementById('sampleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const payload = {
                custom_data: {}
            };

            formData.forEach((value, key) => {
                if (key.startsWith('dynamic_')) {
                    payload.custom_data[key.replace('dynamic_', '')] = value;
                } else {
                    payload[key] = value;
                }
            });

            const response = await fetch('/api/samples', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                e.target.reset();
                document.getElementById('receivable_id').disabled = true; // Selecti sıfırla
                loadSamples();
            } else {
                console.error(result);
                alert('Hata! Konsolu kontrol edin.');
            }
        });
    </script>

</body>

</html>
