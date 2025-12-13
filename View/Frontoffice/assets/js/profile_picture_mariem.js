
document.getElementById("avatarEditBtn").addEventListener("click", function() {
    document.getElementById("avatarInput").click();
});

document.getElementById("avatarInput").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        document.getElementById("avatarPreview").src = URL.createObjectURL(file);
    }
});

