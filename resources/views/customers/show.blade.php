@extends('layouts.master')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-primary border-bottom border-3">
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                    <h3 class="mb-0 text-primary fw-bold text-uppercase">
                        <i class="bi bi-building me-2"></i>{{ $customer->company_name }}
                    </h3>
                    <div class="text-end">
                        <span
                            class="badge bg-{{ $customer->status === 'aktif' ? 'success' : ($customer->status === 'aday' ? 'warning text-dark' : 'danger') }} fs-6 px-3 py-2">
                            {{ strtoupper($customer->status) }}
                        </span>
                        <a href="/customers" class="btn btn-sm btn-outline-secondary ms-3"><i class="bi bi-arrow-left"></i>
                            Listeye Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded" id="customer-tabs">

                        <div class="bg-light text-muted fw-bold p-2 ps-3 border-bottom"
                            style="font-size: 0.85rem; letter-spacing: 1px;">GENEL BAKIŞ</div>
                        <button class="list-group-item list-group-item-action active fw-bold"
                            onclick="loadTab('details', this)">
                            <i class="bi bi-info-square me-2"></i>Müşteri Detayları
                        </button>
                        <button class="list-group-item list-group-item-action fw-bold text-muted" disabled>
                            <i class="bi bi-person-lines-fill me-2"></i>İletişim Kişileri (Yakında)
                        </button>

                        <div class="bg-light text-muted fw-bold p-2 ps-3 border-bottom border-top"
                            style="font-size: 0.85rem; letter-spacing: 1px;">TİCARİ SÜREÇLER</div>
                        <button class="list-group-item list-group-item-action fw-bold"
                            onclick="loadTab('opportunities', this)">
                            <i class="bi bi-currency-dollar me-2"></i>Satış Fırsatları
                        </button>
                        <button class="list-group-item list-group-item-action fw-bold" onclick="loadTab('samples', this)">
                            <i class="bi bi-droplet me-2"></i>Numune Gönderimleri
                        </button>

                        <div class="bg-light text-muted fw-bold p-2 ps-3 border-bottom border-top"
                            style="font-size: 0.85rem; letter-spacing: 1px;">TEKNİK & DESTEK</div>
                        <button class="list-group-item list-group-item-action fw-bold" onclick="loadTab('visits', this)">
                            <i class="bi bi-geo-alt me-2"></i>Saha Ziyaretleri
                        </button>
                        <button class="list-group-item list-group-item-action fw-bold" onclick="loadTab('machines', this)">
                            <i class="bi bi-gear me-2"></i>Makine Parkuru
                        </button>
                        <button class="list-group-item list-group-item-action fw-bold" onclick="loadTab('lab_tests', this)">
                            <i class="bi bi-clipboard-check me-2"></i>Kalite Testleri
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm" style="min-height: 400px;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-secondary" id="tabTitle">Müşteri Detayları</h5>
                </div>
                <div class="card-body" id="tabContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CUSTOMER_ID = {{ $customer->id }};
        const API_HEADERS = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        const contentArea = document.getElementById('tabContent');
        const tabTitle = document.getElementById('tabTitle');

        // HELPER: Alt çizgileri silip ilk harfleri büyütür (Örn: urun_denemesi -> Ürün Denemesi)
        function formatString(str) {
            if (!str) return '-';
            return str.toString()
                .replace(/_/g, ' ')
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(' ');
        }

        // Sayfa yüklendiğinde varsayılan sekmeyi aç
        document.addEventListener('DOMContentLoaded', () => {
            // Vanilla JS ile metin içeriğine göre butonu buluyoruz
            const contactBtn = Array.from(document.querySelectorAll('.list-group-item'))
                .find(btn => btn.textContent.includes("İletişim Kişileri"));

            if (contactBtn) {
                contactBtn.removeAttribute('disabled');
                contactBtn.setAttribute('onclick', "loadTab('contacts', this)");
                contactBtn.classList.remove('text-muted');

                // Parantez içindeki "(Yakında)" yazısını temizleyelim ki şık dursun
                contactBtn.innerHTML = '<i class="bi bi-person-lines-fill me-2"></i>İletişim Kişileri';
            }

            loadTab('details', document.querySelector('.list-group-item.active'));
        });

        // Sekme Değiştirici
        function loadTab(tabName, element) {
            // Aktif butonu güncelle
            document.querySelectorAll('#customer-tabs button').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            tabTitle.innerHTML = element.innerHTML.trim();

            // Yükleniyor durumu
            contentArea.innerHTML =
                `<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>`;

            // İlgili veriyi API'den çek
            switch (tabName) {
                case 'details':
                    fetchDetails();
                    break;
                case 'contacts':
                    fetchContacts();
                    break;
                case 'opportunities':
                    fetchOpportunities();
                    break;
                case 'samples':
                    fetchSamples();
                    break;
                case 'visits':
                    fetchVisits();
                    break;
                case 'machines':
                    fetchMachines();
                    break;
                case 'lab_tests':
                    fetchLabTests();
                    break;
            }
        }
        async function fetchContacts() {
            // HTML İskeletini Çiz (Sol: Form, Sağ: Tablo)
            contentArea.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-secondary">
                        <div class="card-header bg-secondary text-white fw-bold py-2"><i class="bi bi-person-plus me-1"></i>Hızlı Kişi Ekle</div>
                        <div class="card-body">
                            <form id="contactForm">
                                <input type="hidden" name="customer_id" value="${CUSTOMER_ID}">
                                <div class="row">
                                    <div class="col-6 mb-2"><label class="form-label" style="font-size:0.8rem">Ad</label><input type="text" name="first_name" class="form-control form-control-sm" required></div>
                                    <div class="col-6 mb-2"><label class="form-label" style="font-size:0.8rem">Soyad</label><input type="text" name="last_name" class="form-control form-control-sm" required></div>
                                </div>
                                <div class="mb-2"><label class="form-label" style="font-size:0.8rem">Ünvan</label><input type="text" name="title" class="form-control form-control-sm"></div>
                                <div class="mb-2"><label class="form-label" style="font-size:0.8rem">Telefon</label><input type="text" name="phone" class="form-control form-control-sm"></div>
                                <div class="mb-3"><label class="form-label" style="font-size:0.8rem">E-Posta</label><input type="email" name="email" class="form-control form-control-sm"></div>
                                <button type="submit" class="btn btn-sm btn-secondary w-100 fw-bold">Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Ad Soyad</th><th>Ünvan</th><th>İletişim</th></tr></thead>
                        <tbody id="contactsTableBody"><tr><td colspan="3" class="text-center">Yükleniyor...</td></tr></tbody>
                    </table>
                </div>
            </div>
        `;

            // Form Submit Olayını Bağla
            document.getElementById('contactForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = Object.fromEntries(new FormData(e.target).entries());
                const res = await fetch('/api/contacts', {
                    method: 'POST',
                    headers: API_HEADERS,
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    e.target.reset();
                    fetchContactsData();
                } else alert('Kayıt başarısız.');
            });

            // Verileri Çek ve Doldur
            fetchContactsData();
        }

        async function fetchContactsData() {
            const res = await fetch(`/api/contacts?customer_id=${CUSTOMER_ID}`, {
                headers: API_HEADERS
            });
            const result = await res.json();
            const tbody = document.getElementById('contactsTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) return tbody.innerHTML =
                `<tr><td colspan="3" class="text-center">Kayıtlı kişi yok.</td></tr>`;

            result.data.forEach(c => {
                tbody.innerHTML += `<tr>
                <td class="fw-bold"><i class="bi bi-person-circle text-muted me-2"></i>${c.first_name} ${c.last_name}</td>
                <td>${formatString(c.title)}</td>
                <td>
                    ${c.phone ? `<small class="d-block"><i class="bi bi-telephone text-success me-1"></i>${c.phone}</small>` : ''}
                    ${c.email ? `<small class="d-block"><i class="bi bi-envelope text-primary me-1"></i>${c.email}</small>` : ''}
                </td>
            </tr>`;
            });
        }
        // --- 1. Müşteri Detayları (Statik + Dinamik EAV Verileri) ---
        async function fetchDetails() {
            try {
                const res = await fetch(`/api/customers/${CUSTOMER_ID}`, {
                    headers: API_HEADERS
                });
                const result = await res.json();
                const c = result.data;

                // EAV (custom_data) Formatlama
                let customFieldsHtml = '';
                if (c.custom_data && Object.keys(c.custom_data).length > 0) {
                    for (const [key, value] of Object.entries(c.custom_data)) {
                        customFieldsHtml += `
                        <div class="col-md-6 mb-3">
                            <label class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">${key.replace(/_/g, ' ')}</label>
                            <div class="fs-6 text-dark">${value || '-'}</div>
                        </div>`;
                    }
                } else {
                    customFieldsHtml = '<div class="col-12 text-muted">Özel dinamik alan verisi bulunamadı.</div>';
                }

                contentArea.innerHTML = `
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Firma Adı</label>
                        <div class="fs-5 fw-bold text-dark">${c.company_name}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Vergi No</label>
                        <div class="fs-6 text-dark">${c.tax_number || '-'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">E-Posta</label>
                        <div class="fs-6 text-dark">${c.email || '-'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Telefon</label>
                        <div class="fs-6 text-dark">${c.phone || '-'}</div>
                    </div>
                </div>
                <h6 class="border-bottom pb-2 mb-3 fw-bold text-primary"><i class="bi bi-sliders me-2"></i>Dinamik Alanlar (EAV)</h6>
                <div class="row">${customFieldsHtml}</div>
            `;
            } catch (e) {
                contentArea.innerHTML = `<div class="alert alert-danger">Veriler yüklenirken hata oluştu.</div>`;
            }
        }

        // --- 2. Fırsatlar ---
        async function fetchOpportunities() {
            const res = await fetch(`/api/opportunities?customer_id=${CUSTOMER_ID}`, {
                headers: API_HEADERS
            });
            const result = await res.json();
            let html =
                `<table class="table table-hover"><thead><tr><th>Konu</th><th>Tutar</th><th>Aşama</th><th>Karar Tarihi</th></tr></thead><tbody>`;

            if (result.data.length === 0) html +=
                `<tr><td colspan="4" class="text-center py-3">Kayıtlı fırsat yok.</td></tr>`;

            result.data.forEach(o => {
                html += `<tr>
                <td class="fw-bold">${o.title}</td>
                <td class="text-success fw-bold">${o.formatted_amount}</td>
                <td><span class="badge bg-secondary">${o.stage.toUpperCase()}</span></td>
                <td>${o.expected_decision_date || '-'}</td>
            </tr>`;
            });
            contentArea.innerHTML = html + `</tbody></table>`;
        }

        // --- 3. Numuneler (Polimorfik) ---
        async function fetchSamples() {
            const res = await fetch(`/api/samples?receivable_type=App\\Models\\Customer&receivable_id=${CUSTOMER_ID}`, {
                headers: API_HEADERS
            });
            const result = await res.json();
            let html =
                `<table class="table table-hover"><thead><tr><th>Konu</th><th>Statü</th><th>Kargo Firması</th></tr></thead><tbody>`;

            // EAV Verisi üzerinden filtre simülasyonu ya da doğrudan listeleme
            let hasData = false;
            result.data.forEach(s => {
                if (s.receiver && s.receiver.id === CUSTOMER_ID && s.receiver.type ===
                    'App\\Models\\Customer') {
                    hasData = true;
                    html += `<tr>
                    <td class="fw-bold">${s.subject}</td>
                    <td><span class="badge bg-info text-dark">${s.status.toUpperCase()}</span></td>
                    <td class="text-primary">${s.custom_data?.shipping_company || '-'}</td>
                </tr>`;
                }
            });

            if (!hasData) html +=
                `<tr><td colspan="3" class="text-center py-3">Bu müşteriye ait numune gönderimi yok.</td></tr>`;
            contentArea.innerHTML = html + `</tbody></table>`;
        }

        // --- 4. Ziyaretler ---
        async function fetchVisits() {
            const res = await fetch(`/api/visits?customer_id=${CUSTOMER_ID}`, {
                headers: API_HEADERS
            });
            const result = await res.json();
            let html =
                `<table class="table table-hover"><thead><tr><th>Tarih</th><th>Sebep</th><th>Personel</th><th>Barkod (EAV)</th></tr></thead><tbody>`;

            if (result.data.length === 0) html +=
                `<tr><td colspan="4" class="text-center py-3">Kayıtlı ziyaret yok.</td></tr>`;

            result.data.forEach(v => {
                html += `<tr>
                <td class="fw-bold">${v.visit_date}</td>
                <td><span class="badge bg-secondary">${v.reason.toUpperCase()}</span></td>
                <td>${v.user ? v.user.name : '-'}</td>
                <td class="text-primary">${v.custom_data?.barcode_no || '-'}</td>
            </tr>`;
            });
            contentArea.innerHTML = html + `</tbody></table>`;
        }

        // --- 5. Makineler ---
        async function fetchMachines() {
            const res = await fetch(`/api/machines?customer_id=${CUSTOMER_ID}`, {
                headers: API_HEADERS
            });
            const result = await res.json();
            let html =
                `<table class="table table-hover"><thead><tr><th>Marka/Model</th><th>Mülkiyet</th><th>Seri No</th><th>Kurulum</th></tr></thead><tbody>`;

            if (result.data.length === 0) html +=
                `<tr><td colspan="4" class="text-center py-3">Kayıtlı makine yok.</td></tr>`;

            result.data.forEach(m => {
                html += `<tr>
                <td><strong class="text-primary">${m.brand}</strong> <br> <small>${m.model_name}</small></td>
                <td><span class="badge bg-secondary">${m.ownership}</span></td>
                <td>${m.serial_number || '-'}</td>
                <td>${m.installed_at || '-'}</td>
            </tr>`;
            });
            contentArea.innerHTML = html + `</tbody></table>`;
        }

        // --- 6. Kalite Testleri ---
        async function fetchLabTests() {
            const res = await fetch(`/api/lab-tests?customer_id=${CUSTOMER_ID}`, {
                headers: API_HEADERS
            });
            const result = await res.json();
            let html =
                `<table class="table table-hover"><thead><tr><th>Tarih</th><th>Test Tipi</th><th>Durum</th><th>Bağlantı</th></tr></thead><tbody>`;

            if (result.data.length === 0) html +=
                `<tr><td colspan="4" class="text-center py-3">Laboratuvar testi yok.</td></tr>`;

            result.data.forEach(t => {
                let badge = t.status === 'onaylandi' ? 'success' : (t.status === 'red' ? 'danger' : 'warning');
                html += `<tr>
                <td class="fw-bold">${t.test_date}</td>
                <td class="text-danger fw-bold">${t.test_type}</td>
                <td><span class="badge bg-${badge}">${t.status.toUpperCase()}</span></td>
                <td><small>${t.machine ? t.machine.name : 'Bağımsız'}</small></td>
            </tr>`;
            });
            contentArea.innerHTML = html + `</tbody></table>`;
        }
    </script>
@endsection
