</main>
<!-- Main Wrapper End -->

<!-- Back to Top Button -->
<button id="backToTopBtn" title="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
    #backToTopBtn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 45px;
        height: 45px;
        background: var(--accent-color, #b19cd9);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        transition: all 0.3s ease;
        opacity: 0;
    }

    #backToTopBtn.show {
        display: flex;
        opacity: 1;
        animation: fadeInBTT 0.3s forwards;
    }

    @keyframes fadeInBTT {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #backToTopBtn:hover {
        background: #9b85c1;
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }
</style>

<script>
    const bttBtn = document.getElementById("backToTopBtn");
    window.addEventListener("scroll", function () {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            bttBtn.classList.add("show");
        } else {
            bttBtn.classList.remove("show");
        }
    });

    bttBtn.addEventListener("click", function () {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>
</body>

</html>