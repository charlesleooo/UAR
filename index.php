<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Access Request Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            padding: 5px;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-center mb-6">USER ACCESS REQUEST FORM</h1>

            <!-- Requestor Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Requestor Name: *</label>
                    <input type="text" name="requestor_name" required class="mt-1 block w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Unit: *</label>
                    <select name="business_unit" required class="mt-1 block w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Business Unit</option>
                        <option value="AAC">AAC</option>
                        <option value="ALDEV">ALDEV</option>
                        <option value="ARC">ARC</option>
                        <option value="FHI">FHI</option>
                        <option value="SACI">SACI</option>
                        <option value="SAVI">SAVI</option>
                        <option value="SCCI">SCCI</option>
                        <option value="SFC">SFC</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">No. of Access Request: *</label>
                    <input type="number" name="access_request_number" required class="mt-1 block w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department: *</label>
                    <select name="department" required class="mt-1 block w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Department</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Add: *</label>
                    <input type="email" name="email" required class="mt-1 block w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact No.: *</label>
                    <input type="tel" name="contact_number" required class="mt-1 block w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Access Types -->
            <div class="space-y-4">
                <h2 class="text-lg font-medium">Access Type: *</h2>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="system_application" class="text-blue-600 w-5 h-5" required>
                        <span>System Application</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="pc_access_network" class="text-blue-600 w-5 h-5">
                        <span>PC Access - Network</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="email_access" class="text-blue-600 w-5 h-5">
                        <span>Email Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="server_access" class="text-blue-600 w-5 h-5">
                        <span>Server Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="internet_access" class="text-blue-600 w-5 h-5">
                        <span>Internet Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="printer_access" class="text-blue-600 w-5 h-5">
                        <span>Printer Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="active_directory" class="text-blue-600 w-5 h-5">
                        <span>Active Directory Access (MS ENTRA ID)</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="firewall_access" class="text-blue-600 w-5 h-5">
                        <span>Firewall Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="wifi_access" class="text-blue-600 w-5 h-5">
                        <span>Wi-Fi/Access Point Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="tna_biometric" class="text-blue-600 w-5 h-5">
                        <span>TNA Biometric Device Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="usb_pc_port" class="text-blue-600 w-5 h-5">
                        <span>USB/PC-port Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="cctv_access" class="text-blue-600 w-5 h-5">
                        <span>CCTV Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="pc_access_local" class="text-blue-600 w-5 h-5">
                        <span>PC Access - Local</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="vpn_access" class="text-blue-600 w-5 h-5">
                        <span>VPN Access</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="access_type" value="offsite_storage" class="text-blue-600 w-5 h-5">
                        <span>Offsite Storage Facility Access</span>
                    </label>
                </div>
            </div>

            <!-- System/Application Type -->
            <div id="systemApplicationSection" class="space-y-4 hidden">
                <h2 class="text-lg font-medium">System/Application Type:</h2>
                <div class="grid grid-cols-4 gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="erp_nav" class="rounded text-blue-600 w-5 h-5">
                        <span>ERP/NAV</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="legacy_payroll" class="rounded text-blue-600 w-5 h-5">
                        <span>Legacy Payroll</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="hris" class="rounded text-blue-600 w-5 h-5">
                        <span>HRIS</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="legacy_purchasing" class="rounded text-blue-600 w-5 h-5">
                        <span>Legacy Purchasing</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="piece_rate_payroll" class="rounded text-blue-600 w-5 h-5">
                        <span>Piece Rate Payroll System</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="legacy_inventory" class="rounded text-blue-600 w-5 h-5">
                        <span>Legacy Inventory</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="fresh_chilled" class="rounded text-blue-600 w-5 h-5">
                        <span>Fresh Chilled Receiving System</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="legacy_vouchering" class="rounded text-blue-600 w-5 h-5">
                        <span>Legacy Vouchering</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="quickbooks" class="rounded text-blue-600 w-5 h-5">
                        <span>Quickbooks</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="legacy_ledger" class="rounded text-blue-600 w-5 h-5">
                        <span>Legacy Ledger System</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="zankpos" class="rounded text-blue-600 w-5 h-5">
                        <span>ZankPOS</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="system_type[]" value="other" class="rounded text-blue-600 w-5 h-5">
                        <span>Other (specify):</span>
                        <input type="text" name="other_system_type" class="ml-2 w-full h-12 text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                    </div>
                </div>
            </div>
            
            <!-- Role Access Type -->
            <div id="roleAccessSection" class="space-y-4 hidden">
                <h2 class="text-lg font-medium">Role Access Type (If applicable):</h2>
                <div>
                    <textarea name="role_access_type" rows="4" class="resize-none w-full text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500 p-4" placeholder="Enter role access type details"></textarea>
                </div>
            </div>

            <!-- Access Duration -->
            <div class="space-y-4">
                <h2 class="text-lg font-medium">Access Duration: *</h2>
                <div class="space-y-4">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="duration_type" value="permanent" class="text-blue-600 w-5 h-5" required>
                        <span class="text-lg">Permanent</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="radio" name="duration_type" value="temporary" class="text-blue-600 w-5 h-5">
                        <span class="text-lg">Temporary</span>
                        <div class="flex items-center space-x-2 ml-4">
                            <input type="date" 
                                name="start_date" 
                                class="h-12 text-lg w-48 rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                disabled>
                            <span class="text-lg">to</span>
                            <input type="date" 
                                name="end_date" 
                                class="h-12 text-lg w-48 rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                disabled>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Justification -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Justification for access request: *</label>
                <textarea name="justification" required rows="4" class="resize-none mt-1 block w-full text-lg rounded-md border-2 border-black shadow-sm focus:border-blue-500 focus:ring-blue-500 p-4"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="reset" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 h-12 text-lg">Reset</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 h-12 text-lg">Submit</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitDepartments = {
                'AAC': [
                    'OFFICE OF THE VP-OPERATIONS',
                    'INFORMATION TECHNOLOGY (IT)',
                    'FINANCE',
                    'ENGINEERING',
                    'HUMAN RESOURCE & ADMIN',
                    'MATERIALS MANAGEMENT',
                    'TECHNICAL SERVICES',
                    'REGIONAL SALES',
                    'SALES & MARKETING',
                    'LAND BASED',
                    'SEA CAGE',
                    'GROW OUT',
                    'APP',
                    'RPP',
                    'GENSAN PROCESSING PLANT (GPP)',
                    'PPP-SLICING/OTHER PROCESSING',
                    'MANUFACTURING',
                    'VAP',
                    'MARKETING',
                    'AFFILIATES',
                    'TH - CLEARING',
                    'TILAPIA HATCHERY (TH)',
                    'CATFISH GROW-OUT',
                    'SPECIAL IMPORTATION/TRADING',
                ],
                'ALDEV': [
                    'ALD Cattle',
                    'ALD Banana-San Jose',
                    'ALD Engineering',
                    'ALD Operations Services',
                    'ALD Technical Services',
                    'ALD-PROD PLANNING',
                ],
                'ARC': [
                    'ARC - NURSERY',
                    'ARC Engineering',
                    'ARC Growout',
                    'Administrative services',
                ],
                'FHI': [
                    'FIELDS',
                    'SELLING & MARKETING DEPARTMENT',
                    'OPERATIONS DEPARTMENT',
                    'OTHER SPECIE DEPARTMENT',
                ],
                'SACI': [
                    'ALDEVINCO-AGRI',
                    'FHI',
                    'ARC',
                    'SCCI',
                    'CLAFI',
                    'ALSEC',
                    'SAVI',
                    'CONAL',
                    'ABBA BLESS',
                    'ALC',
                    'SBSTG',
                    'G3 AQUAVENTURES INC',
                    'AAC',
                    'VARIOUS AFFILIATES',
                ],
                'SAVI': [
                    'SCCI',
                    'ALSEC',
                    'SUNFARMS',
                    'AAC',
                    'OPERATIONS SERVICES',
                    'BANANA OPERATION',
                    'BANANA LEAVES OPERATION',
                    'AGRI-ENGINEERING',
                    'G&A',
                    'TSD Agri',
                    'G&A - Distribution',
                    'OOM',
                    'Conal Corporation',
                ],
                'SCCI': [
                    'SCC Banana-Lanton',
                    'SCC Cattle',
                    'SCC Engineering',
                    'SCC Pineapple',
                    'SCC Technical Services',
                    'SCCI Operations Services',
                ],
                'SFC': [
                    'AGRI-ENGINEERING',
                    'AGRI-OPERATIONS SERVICES',
                    'AGRI-PINEAPPLE OPERATIONS',
                    'FIELD OVERHEAD',
                ]

            };

            const businessUnitSelect = document.querySelector('select[name="business_unit"]');
            const departmentSelect = document.querySelector('select[name="department"]');

            businessUnitSelect.addEventListener('change', function() {
                const selectedUnit = this.value;
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                
                if (selectedUnit && businessUnitDepartments[selectedUnit]) {
                    businessUnitDepartments[selectedUnit].forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept;
                        option.textContent = dept;
                        departmentSelect.appendChild(option);
                    });
                }
            });

            const form = document.getElementById('accessRequestForm');
            const durationType = form.querySelectorAll('input[name="duration_type"]');
            const startDate = form.querySelector('input[name="start_date"]');
            const endDate = form.querySelector('input[name="end_date"]');
            const systemApplicationSection = document.getElementById('systemApplicationSection');
            const roleAccessSection = document.getElementById('roleAccessSection');
            const accessTypeInputs = form.querySelectorAll('input[name="access_type"]');
            const otherSystemTypeCheckbox = form.querySelector('input[value="other"]');
            const otherSystemTypeInput = form.querySelector('input[name="other_system_type"]');

            // Add date validation
            const startDateInput = form.querySelector('input[name="start_date"]');
            const endDateInput = form.querySelector('input[name="end_date"]');

            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    endDateInput.min = this.value;
                });

                endDateInput.addEventListener('change', function() {
                    startDateInput.max = this.value;
                });
            }

            // Function to reset form sections
            function resetFormSections() {
                // Reset and hide system application section
                systemApplicationSection.classList.add('hidden');
                form.querySelectorAll('input[name="system_type[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                otherSystemTypeInput.value = '';
                otherSystemTypeInput.disabled = true;

                // Reset and hide role access section
                roleAccessSection.classList.add('hidden');
                form.querySelector('textarea[name="role_access_type"]').value = '';

                // Reset access duration section
                startDate.disabled = true;
                startDate.value = '';
                endDate.disabled = true;
                endDate.value = '';
            }

            // Handle Other System Type input
            otherSystemTypeCheckbox?.addEventListener('change', function() {
                otherSystemTypeInput.disabled = !this.checked;
                if (this.checked) {
                    otherSystemTypeInput.required = true;
                } else {
                    otherSystemTypeInput.required = false;
                    otherSystemTypeInput.value = '';
                }
            });

            // Handle System Application section visibility
            accessTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    resetFormSections();
                    if (this.value === 'system_application') {
                        systemApplicationSection.classList.remove('hidden');
                        roleAccessSection.classList.remove('hidden');
                    }
                });
            });

            durationType.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'temporary') {
                        startDate.disabled = false;
                        endDate.disabled = false;
                        startDate.required = true;
                        endDate.required = true;
                    } else {
                        startDate.disabled = true;
                        endDate.disabled = true;
                        startDate.required = false;
                        endDate.required = false;
                        startDate.value = '';
                        endDate.value = '';
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const accessTypes = form.querySelectorAll('input[name="access_type"]:checked');
                if (accessTypes.length === 0) {
                    alert('Please select an Access Type');
                    return;
                }

                // Validate system application specific fields
                if (accessTypes[0].value === 'system_application') {
                    // Validate system types
                    const systemTypes = form.querySelectorAll('input[name="system_type[]"]:checked');
                    if (systemTypes.length === 0) {
                        alert('Please select at least one System/Application Type');
                        return;
                    }

                    // Validate access duration
                    const durationTypeSelected = form.querySelector('input[name="duration_type"]:checked');
                    if (!durationTypeSelected) {
                        alert('Please select an Access Duration');
                        return;
                    }

                    if (durationTypeSelected.value === 'temporary') {
                        if (!startDate.value || !endDate.value) {
                            alert('Please enter both start and end dates');
                            return;
                        }
                    }
                }

                // If all validations pass, submit the form
                this.submit();
            });
        });
    </script>
</body>
</html>