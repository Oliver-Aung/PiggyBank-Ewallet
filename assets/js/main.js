document.addEventListener('DOMContentLoaded', function() {
  // Mobile menu toggle
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  
  if (mobileMenuBtn && mobileMenu) {
      mobileMenuBtn.addEventListener('click', function() {
          mobileMenu.classList.toggle('hidden');
      });
  }
  
  // Transaction form handling
  const transactionForm = document.getElementById('transaction-form');
  if (transactionForm) {
      transactionForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(transactionForm);
          const transactionType = document.getElementById('transaction-type').value;
          
          // Validate form
          if (formData.get('amount') <= 0) {
              showAlert('Amount must be greater than zero', 'error');
              return;
          }
          
          // Simulate form submission (in a real app, this would be an AJAX call)
          showAlert(`Transaction ${transactionType} submitted successfully!`, 'success');
          transactionForm.reset();
      });
  }
  
  // Tab switching
  const tabs = document.querySelectorAll('[data-tab]');
  tabs.forEach(tab => {
      tab.addEventListener('click', function() {
          const tabId = this.getAttribute('data-tab');
          const tabContent = document.getElementById(tabId);
          
          // Hide all tab contents
          document.querySelectorAll('.tab-content').forEach(content => {
              content.classList.add('hidden');
          });
          
          // Deactivate all tabs
          document.querySelectorAll('[data-tab]').forEach(t => {
              t.classList.remove('active');
          });
          
          // Show selected tab content
          tabContent.classList.remove('hidden');
          this.classList.add('active');
      });
  });
  
  // Show alert function
  window.showAlert = function(message, type) {
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type}`;
      alertDiv.textContent = message;
      
      const alertsContainer = document.getElementById('alerts-container');
      alertsContainer.appendChild(alertDiv);
      
      setTimeout(() => {
          alertDiv.remove();
      }, 5000);
  }
  
  // Chart initialization (for dashboard)
  const balanceChart = document.getElementById('balance-chart');
  if (balanceChart) {
      // In a real app, you would use Chart.js or similar
      console.log('Chart would be initialized here with real data');
  }
});

// Back to top button
const backToTopButton = document.getElementById('back-to-top');
if (backToTopButton) {
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.style.display = 'flex';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}