import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Payment = () => {
  const [paymentData, setPaymentData] = useState({
    amount: '',
    name: '',
    email: ''
  });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Load Midtrans Snap script
    const script = document.createElement('script');
    script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
    script.setAttribute('data-client-key', 'Mid-client-Xxt3B_FpFt_U8GHR');
    document.head.appendChild(script);

    return () => {
      document.head.removeChild(script);
    };
  }, []);

  const handleInputChange = (e) => {
    setPaymentData({
      ...paymentData,
      [e.target.name]: e.target.value
    });
  };

  const handlePayment = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      const response = await axios.post('http://localhost:8000/api/payment', paymentData);
      const { token } = response.data;

      window.snap.pay(token, {
        onSuccess: (result) => {
          console.log('Payment success:', result);
          alert('Pembayaran berhasil!');
        },
        onPending: (result) => {
          console.log('Payment pending:', result);
          alert('Pembayaran pending, silakan selesaikan pembayaran.');
        },
        onError: (result) => {
          console.log('Payment error:', result);
          alert('Pembayaran gagal!');
        },
        onClose: () => {
          console.log('Payment popup closed');
        }
      });
    } catch (error) {
      console.error('Error creating payment:', error);
      alert('Gagal membuat pembayaran');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
      <h2 className="text-2xl font-bold mb-6 text-center">Payment</h2>
      
      <form onSubmit={handlePayment} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Nama
          </label>
          <input
            type="text"
            name="name"
            value={paymentData.name}
            onChange={handleInputChange}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Masukkan nama"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input
            type="email"
            name="email"
            value={paymentData.email}
            onChange={handleInputChange}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Masukkan email"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Jumlah (Rp)
          </label>
          <input
            type="number"
            name="amount"
            value={paymentData.amount}
            onChange={handleInputChange}
            required
            min="1000"
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Masukkan jumlah pembayaran"
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
        >
          {loading ? 'Processing...' : 'Bayar Sekarang'}
        </button>
      </form>
    </div>
  );
};

export default Payment;