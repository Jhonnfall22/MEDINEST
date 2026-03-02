<?php
session_start();
require_once "config/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Pets | VetClinic</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root { --admin-primary: #220bef; --bg-body: #f4f7fa; --border-color: #e0e0e0; }
body { font-family: 'Poppins', sans-serif; background-color: var(--bg-body); color: #333; margin:0; }
.navbar-brand span { color: var(--admin-primary); font-weight:700; }
.page-header { margin-top:100px; margin-bottom:30px; }
.stat-card { background:#fff; border:1px solid var(--border-color); border-radius:12px; padding:20px; text-align:center; transition: transform 0.2s ease; }
.stat-card:hover { transform: translateY(-3px); }
.stat-card small { font-size:0.75rem; color:#777; text-transform:uppercase; font-weight:700; letter-spacing:0.5px; }
.stat-card h3 { font-weight:700; margin:5px 0 0; color:#222; }
.pet-card { background:#fff; border:1px solid var(--border-color); border-radius:16px; padding:25px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; transition:all 0.2s ease; }
.pet-card:hover { border-color:var(--admin-primary); box-shadow:0 5px 15px rgba(0,0,0,0.05); }
.pet-info h5 { font-weight:700; margin-bottom:12px; color:var(--admin-primary); }
.pet-details-grid { display:grid; grid-template-columns:auto auto; gap:10px 30px; }
.pet-details-grid span { font-size:0.9rem; }
.pet-details-grid strong { color:#555; font-weight:600; }
.status-badge { padding:8px 16px; border-radius:30px; font-weight:600; font-size:0.75rem; text-transform:uppercase; }
.status-completed { background-color:#d1e7dd; color:#0f5132; }
.status-pending { background-color:#fff3cd; color:#664d03; }
@media (max-width:768px){ .pet-card{ flex-direction:column; align-items:flex-start; } .status-badge{ margin-top:20px; } }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top shadow-sm bg-white py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Vet<span>clinic</span></a>
        <div class="ms-auto">
            <a href="user_home.php" class="btn btn-sm btn-light border fw-medium me-2">Home</a>
            <a href="mypets.php" class="btn btn-sm btn-primary fw-medium" style="background-color: var(--admin-primary); border:none;">My Pets</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="page-header text-center">
        <h2 class="fw-bold">My Pets</h2>
        <p class="text-muted">Track your pet registration and appointment status.</p>
    </div>

    <div class="row g-3 mb-5 justify-content-center">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <small>Total Pets</small>
                <h3 id="totalPets">0</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-bottom:3px solid #198754;">
                <small>Completed</small>
                <h3 id="completedPets" class="text-success">0</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-bottom:3px solid #ffc107;">
                <small>Pending</small>
                <h3 id="pendingPets" class="text-warning">0</h3>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9" id="petList"></div>
    </div>
</div>

<script>
const petList = document.getElementById('petList');

function renderPets(data){
    petList.innerHTML = '';
    data.pets.forEach(pet=>{
        const isCompleted = pet.status==='completed';
        const card = document.createElement('div');
        card.classList.add('pet-card');
        card.innerHTML=`
            <div class="pet-info">
                <h5><i class="bi bi-paw-fill me-2"></i>${pet.name}</h5>
                <div class="pet-details-grid">
                    <span><strong>Type:</strong> ${pet.type}</span>
                    <span><strong>Breed:</strong> ${pet.breed}</span>
                    <span><strong>Age:</strong> ${pet.age} Years</span>
                    <span><strong>Clinic:</strong> ${pet.clinic}</span>
                </div>
            </div>
            <div class="status-badge ${isCompleted?'status-completed':'status-pending'}">
                <i class="bi ${isCompleted?'bi-check-circle-fill':'bi-clock-history'} me-1"></i>
                ${pet.status}
            </div>
        `;
        petList.appendChild(card);
    });

    document.getElementById('totalPets').innerText = data.total;
    document.getElementById('completedPets').innerText = data.completed;
    document.getElementById('pendingPets').innerText = data.pending;
}

async function fetchPets(){
    try{
        const response = await fetch('get_pets.php');
        const data = await response.json();
        renderPets(data);
    } catch(e){
        console.error('Error fetching pets:',e);
    }
}

document.addEventListener('DOMContentLoaded', fetchPets);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>