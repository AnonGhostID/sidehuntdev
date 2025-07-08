/**
 * Balance Update Handler
 * Automatically updates the displayed balance when changes occur
 */
class BalanceUpdater {
    constructor() {
        this.balanceElements = document.querySelectorAll('[data-balance]');
        this.init();
    }

    init() {
        // Listen for custom balance update events
        document.addEventListener('balanceUpdated', (event) => {
            this.updateBalance(event.detail.newBalance);
        });        // Balance polling disabled to prevent unnecessary API calls
        // setInterval(() => {
        //     this.checkBalanceUpdate();
        // }, 30000);
    }

    /**
     * Update balance display elements
     * @param {number} newBalance - The new balance amount
     */
    updateBalance(newBalance) {
        this.balanceElements.forEach(element => {
            const formattedBalance = this.formatCurrency(newBalance);
            element.textContent = formattedBalance;
        });
    }

    /**
     * Format number as Indonesian Rupiah
     * @param {number} amount - The amount to format
     * @returns {string} Formatted currency string
     */
    formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    /**
     * Check for balance updates via AJAX
     */
    async checkBalanceUpdate() {
        try {
            const response = await fetch('/api/user/balance', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.balance !== undefined) {
                    this.updateBalance(data.balance);
                }
            }
        } catch (error) {
            console.log('Balance check failed:', error);
            // Fail silently to avoid disrupting user experience
        }
    }

    /**
     * Trigger balance update event
     * @param {number} newBalance - The new balance amount
     */
    static triggerUpdate(newBalance) {
        const event = new CustomEvent('balanceUpdated', {
            detail: { newBalance: newBalance }
        });
        document.dispatchEvent(event);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new BalanceUpdater();
});

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BalanceUpdater;
}
