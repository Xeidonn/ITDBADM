<?php
    session_start();
    
    // Destroy the session
    session_unset();
    session_destroy();

    // Clear cart data from localStorage (handled by JavaScript)
    echo "<script>
        localStorage.removeItem('cart');  // Clear the cart from localStorage
        window.location.href = 'index.php';  // Redirect to the homepage
    </script>";
?>
