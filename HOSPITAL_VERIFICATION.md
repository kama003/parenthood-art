# Hospital Functionality Verification Report

## 1. Inventory Visibility
**Requirement:** "Can view only its own inventory"
**Status:** ✅ **Verified**
- **Implementation:** In `App\Livewire\Samples\Index::render()`, there is an explicit check:
  ```php
  if ($user->isHospital()) {
      $query->where('user_id', $user->id);
  }
  ```
- **Result:** Hospitals only see samples associated with their User ID in the inventory list.

## 2. Order Placement
**Requirement:** "Can place orders for samples"
**Status:** ✅ **Verified**
- **Implementation:** The `App\Livewire\HospitalOrders\Index` component provides a "New Order" modal.
- **Result:** Hospitals can initiate orders for available samples.

## 3. Order Generation Requirements
**Requirement:** "Enter complete couple/patient details"
**Status:** ⚠️ **Partial Fail / Needs Improvement**
- **Current Behavior:** The order form requires selecting an **existing** couple from a dropdown (`Couple::all()`).
- **Issues:**
  1.  **Workflow:** Users cannot enter new patient details *while* generating the order. They must exit, go to the "Couples" page, create the couple, and return.
  2.  **Privacy:** The current dropdown shows **ALL** couples in the system (from all hospitals/sources), which likely violates data privacy requirements paralleling the "own inventory" rule.
- **Recommendation:**
  - Update `Couple` model to belong to a User (Hospital).
  - Modify the Order Form to allow entering Patient/Couple details inline (or filter the dropdown to only show the Hospital's own patients).

**Requirement:** "Upload Aadhaar photos"
**Status:** ✅ **Verified**
- **Implementation:** The form includes a file input for `aadhaarFile`.
- **Validation:** `required|image|max:2048` ensures an image is uploaded.

**Requirement:** "Fill a declaration form"
**Status:** ✅ **Verified**
- **Implementation:** A required checkbox "I declare these samples are for the specified patients" is present (`declarationAccepted`).

---

## Suggested Actions
1.  **Fix Couple Privacy:** Add `user_id` to `couples` table and filter queries.
2.  **Improve Order Flow:** implementation of an inline "Add Couple" form or fields within the "New Order" modal to satisfy "Enter details... while generating order".
