function kiemTraIDNV() {
    const idInput = document.getElementById('idnv');
    const feedback = document.getElementById('idnv-feedback');
    const submitBtn = document.getElementById('submitBtn');
    const regex = /^NV\d{2,}$/i;
    let timer = null;

    function setInvalid(msg){
        feedback.textContent = msg;
        feedback.style.color = 'red';
        submitBtn.disabled = true;
    }
    function setValid(msg){
        feedback.textContent = msg;
        feedback.style.color = 'green';
        submitBtn.disabled = false;
    }

    function checkRemote(id){
        // tránh gọi trùng
        if (id === lastChecked) return;
        lastChecked = id;
        fetch('check_idnv.php?id=' + encodeURIComponent(id), {cache: 'no-store'})
        .then(r => r.json())
        .then(j => {
            if (!j.ok) {
                if (j.error === 'format') setInvalid('Sai định dạng ID (VD: NV01)');
                else setInvalid('Lỗi kiểm tra');
                return;
            }
            if (j.exists) {
                setInvalid('IDNV đã tồn tại');
            } else {
                setValid('ID hợp lệ và chưa tồn tại');
            }
        })
        .catch(() => setInvalid('Không thể kiểm tra server'));
    }

    let id = idInput.value;
    idInput.addEventListener('input', function(){
        if(!regex.test(id)) {
            feedback.textContent = "Sai dinh dang ID (VD: NV01)";
            feedback.style.color = 'red';
            submitBtn.disabled = true;
        }
        else {
            feedback.textContent = '';
            feedback.style.color = 'green';
            submitBtn.disabled = false;
            clearTimeout(timer);
            timer = setTimeout(() => checkRemote(v), 400);
        }
    });

    
}