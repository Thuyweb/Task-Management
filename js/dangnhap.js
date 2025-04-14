document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.querySelector(".login-container form");
  const registerForm = document.querySelector(".register-container form");
  const container = document.getElementById("container");

  // Chuyển đổi giao diện giữa đăng nhập và đăng ký
  document.getElementById("register").addEventListener("click", () => {
    container.classList.add("right-panel-active");
  });

  document.getElementById("login").addEventListener("click", () => {
    container.classList.remove("right-panel-active");
  });

  // Kiểm tra dữ liệu đầu vào
  function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll(
      'input[type="email"], input[type="password"]'
    );
    inputs.forEach((input) => {
      if (input.value.trim() === "") {
        input.style.border = "1px solid red";
        isValid = false;
      } else {
        input.style.border = "";
      }
    });
    return isValid;
  }

  // Xử lý sự kiện submit cho form đăng nhập
  loginForm.addEventListener("submit", function (event) {
    if (!validateForm(this)) {
      event.preventDefault();
    }
  });

  // Xử lý sự kiện submit cho form đăng ký
  registerForm.addEventListener("submit", function (event) {
    if (!validateForm(this)) {
      event.preventDefault();
    }
  });
});
