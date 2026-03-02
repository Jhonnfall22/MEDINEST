<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pet Information | Vet Clinics</title>

<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
}

.clinic-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 10px;
  padding: 30px 20px;
  background: #fff;
  text-align: center;
  border: 1px solid #ddd;
}

.clinic-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.clinic-card i {
  font-size: 50px;
}

.clinic-card h5 {
  margin-top: 15px;
  font-weight: 600;
}

.clinic-card p {
  margin-bottom: 0;
  color: #777;
}

.clinic-card.selected {
  border-color: #4CAF50;
  box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
}

#petFormSection {
  display: none; /* Hide form initially */
}
</style>
</head>

<body>

<div class="container mt-5">

  <!-- ===== CLINIC SELECTION ===== -->
  <section id="clinicSelection" class="py-5">
    <h2 class="text-center mb-4">Select a Clinic</h2>
    <div class="row g-4 justify-content-center">

      <div class="col-md-4">
        <div class="card clinic-card" onclick="selectClinic('PawCare Vet Clinic', this)">
          <i class="bi bi-hospital text-success"></i>
          <h5>PawCare Vet Clinic</h5>
          <p>Dasmariñas City</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card clinic-card" onclick="selectClinic('Happy Tails Veterinary', this)">
          <i class="bi bi-hospital text-primary"></i>
          <h5>Happy Tails Veterinary</h5>
          <p>Bacoor City</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card clinic-card" onclick="selectClinic('AnimalCare Center', this)">
          <i class="bi bi-hospital text-danger"></i>
          <h5>AnimalCare Center</h5>
          <p>Imus City</p>
        </div>
      </div>

    </div>
  </section>

  <!-- ===== PET INFORMATION FORM ===== -->
  <section id="petFormSection" class="section bg-light py-5">
    <h2 class="text-center mb-2">Pet Information Form</h2>
    <p class="text-center text-muted mb-4">
      Clinic Selected: <strong id="clinicName"></strong>
    </p>

    <form class="row g-3" style="max-width:700px;margin:auto;">
      <div class="col-md-6">
        <label class="form-label">Pet Name</label>
        <input type="text" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Pet Type</label>
        <select class="form-select" required>
          <option value="">Select</option>
          <option>Dog</option>
          <option>Cat</option>
          <option>Other</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Breed</label>
        <input type="text" class="form-control">
      </div>

      <div class="col-md-6">
        <label class="form-label">Age</label>
        <input type="number" class="form-control">
      </div>

      <div class="col-12">
        <label class="form-label">Concern / Reason for Visit</label>
        <textarea class="form-control" rows="3"></textarea>
      </div>

      <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary px-5">Submit</button>
      </div>
    </form>
  </section>

</div>

<script>
function selectClinic(clinicName, element) {
  // Highlight selected clinic
  document.querySelectorAll('.clinic-card').forEach(card => card.classList.remove('selected'));
  element.classList.add('selected');

  // Set the clinic name in the form
  document.getElementById('clinicName').innerText = clinicName;

  // Hide clinic selection and show form
  document.getElementById('clinicSelection').style.display = 'none';
  document.getElementById('petFormSection').style.display = 'block';
}
</script>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
