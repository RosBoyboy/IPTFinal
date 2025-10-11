import React from 'react';

const dashboardImages = [
  '/images/screenshots/151843.png',
  '/images/screenshots/152728.png',
  '/images/screenshots/153006.png',
  '/images/screenshots/201231.png'
];

const Dashboard = () => {
  return (
    <div className="dashboard-container">
      <h2>Dashboard</h2>
      <div className="dashboard-images">
        {dashboardImages.map((src, idx) => (
          <img key={idx} src={src} alt={`Dashboard ${idx + 1}`} className="dashboard-img" />
        ))}
      </div>
    </div>
  );
};

export default Dashboard;
