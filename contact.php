<?php
$page_title = "Contact Us";
include __DIR__ . '/includes/header.php';
?>
<br><br><br><br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Contact Us</h2>
        </div>
        <div class="card-body">
            <p>If you have any questions or need assistance, feel free to reach out to us:</p>
            <h4>Email</h4>
            <p><a href="mailto:pyaesonechantharaung25@gmail.com">pyaesonechantharaung25@gmail.com</a></p>
            <h4>Phone</h4>
            <p>+63 945 354 3153</p>
            <h4>Address</h4>
            <p>Davao City, Philippines</p>
            <h4>Contact Form</h4>
            <form action="contact.php" method="POST">
                <div class="form-group mb-3">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>