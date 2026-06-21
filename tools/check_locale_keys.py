import json
from pathlib import Path

keys = [
    "Total Logs",
    "Deleted",
    "Updated",
    "DRAFT",
    "Pending",
    "Created",
    "Supplier",
    "Safe",
    "Cash",
    "Storage",
    "Invoice",
    "Personal Information",
    "Compensation",
    "Settings",
    "Record daily sales total from employee amounts with one description for the full sale.",
    "Daily sales workspace",
    "Amount Spent by Store (Optional)",
    "Total Amount",
    "Net",
    "Paid history",
    "Base compensation",
    "Sales/performance bonus",
    "Additional benefits",
    "Payroll deductions and advances",
    "Basic salary + commission + allowances",
    "Advances + deduction records",
    "Total payable",
    "Total Before Deductions",
    " Assigned Branches",
    " Commission Transactions",
    " Advances Transactions",
    " No advance transactions for this employee.",
    "Employee name",
    "No storages are linked to this branch yet.",
    "No invoices are linked to this branch yet.",
    "No customers are linked to this branch yet.",
    "No paid commissions yet.",
    "Last commission",
    "No eligible employees are available for first-time commission creation. Open an existing employee commission profile to add more commissions.",
    "Use this only to create the first commission profile for an employee.",
    "Add a new commission for this employee from here. The general create page is only for first-time commission profiles.",
    "Location Information",
    "Referans",
    "Storage Configuration",
    "Description",
    "Total Outcome",
    "Total Income",
]
for fn in [Path('resources/lang/tr.json'), Path('resources/lang/ar.json')]:
    data = json.loads(fn.read_text('utf-8'))
    missing = [k for k in keys if k not in data]
    print(fn, len(data), 'missing', len(missing))
    for k in missing:
        print('  ', k)
    print()