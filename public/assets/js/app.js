async function initDashboard() {
  const data = await fetch('assets/data/sample-data.json').then((res) => res.json());

  document.getElementById('totalStudents').textContent = data.stats.totalStudents;
  document.getElementById('totalRecords').textContent = data.stats.totalRecords;
  document.getElementById('averageGpa').textContent = data.stats.averageGpa.toFixed(2);
  document.getElementById('averageMarks').textContent = `${data.stats.averageMarks.toFixed(1)}%`;
  document.getElementById('topPerformer').textContent = `Top performer • ${data.topPerformer.name}`;
  document.getElementById('topName').textContent = data.topPerformer.name;
  document.getElementById('topScore').textContent = data.topPerformer.score.toFixed(2);

  const recentBody = document.getElementById('recentTableBody');
  recentBody.innerHTML = data.recentEvaluations.map((row) => `
    <tr>
      <td>${row.student}</td>
      <td>${row.subject}</td>
      <td>${row.totalMarks.toFixed(1)}</td>
      <td>${row.grade}</td>
      <td>${row.gpa.toFixed(2)}</td>
    </tr>
  `).join('');

  const performanceCtx = document.getElementById('performanceChart').getContext('2d');
  new Chart(performanceCtx, {
    type: 'line',
    data: {
      labels: ['Assignment', 'Midterm', 'Practical', 'Final'],
      datasets: [{
        label: 'Average Score',
        data: data.performanceTrend,
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37,99,235,0.15)',
        tension: 0.35,
        fill: true,
        pointRadius: 5,
        pointBackgroundColor: '#2563eb'
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
  });

  const gradeCtx = document.getElementById('gradeChart').getContext('2d');
  new Chart(gradeCtx, {
    type: 'doughnut',
    data: {
      labels: Object.keys(data.gradeDistribution),
      datasets: [{
        data: Object.values(data.gradeDistribution),
        backgroundColor: ['#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#8b5cf6']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
  });

  const subjectCtx = document.getElementById('subjectChart').getContext('2d');
  new Chart(subjectCtx, {
    type: 'bar',
    data: {
      labels: data.subjectTrend.map((item) => item.subject),
      datasets: [{
        label: 'Average Marks',
        data: data.subjectTrend.map((item) => item.avgMarks),
        backgroundColor: ['#2563eb', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, max: 100 } }
    }
  });
}

initDashboard();
