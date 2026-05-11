(function () {
    "use strict";

    /* ── Live date ─────────────────────────────────────────────── */
    const el = document.getElementById("liveDate");
    if (el) {
        el.textContent = new Date().toLocaleDateString("en-AU", {
            weekday: "short",
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    }

    /* ── Shared chart defaults ─────────────────────────────────── */
    Chart.defaults.font.family =
        getComputedStyle(document.body).fontFamily || "inherit";
    Chart.defaults.color = "#8a93a5";

    /* ── Trend line chart (School Visits: This Week vs Last Week) ── */
    const trendCtx = document.getElementById("trendChart");
    if (trendCtx) {
        const thisWeek = JSON.parse(trendCtx.dataset.thisWeek || "[]");
        const lastWeek = JSON.parse(trendCtx.dataset.lastWeek || "[]");

        // Build Mon–Sun date labels for this week and last week
        const monday = new Date();
        monday.setDate(monday.getDate() - ((monday.getDay() + 6) % 7)); // rewind to Monday
        monday.setHours(0, 0, 0, 0);

        const fmt = (d) =>
            d.toLocaleDateString("en-AU", { day: "numeric", month: "short" });

        const thisWeekDates = Array.from({ length: 7 }, (_, i) => {
            const d = new Date(monday);
            d.setDate(monday.getDate() + i);
            return fmt(d);
        });

        const lastWeekDates = Array.from({ length: 7 }, (_, i) => {
            const d = new Date(monday);
            d.setDate(monday.getDate() - 7 + i);
            return fmt(d);
        });

        new Chart(trendCtx, {
            type: "line",
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                datasets: [
                    {
                        label: "This Week",
                        data: thisWeek,
                        borderColor: "#4361ee",
                        backgroundColor: "rgba(67,97,238,.10)",
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#4361ee",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                    },
                    {
                        label: "Last Week",
                        data: lastWeek,
                        borderColor: "#a5b4fc",
                        backgroundColor: "rgba(165,180,252,.06)",
                        borderWidth: 2,
                        borderDash: [5, 4],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: "#a5b4fc",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: "index",
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => items[0].label,
                            label: (item) => {
                                const date =
                                    item.datasetIndex === 0
                                        ? thisWeekDates[item.dataIndex]
                                        : lastWeekDates[item.dataIndex];
                                const v = item.parsed.y;
                                return ` ${item.dataset.label} (${date}): ${v} visit${v !== 1 ? "s" : ""}`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: "#f1f3f7", drawBorder: false },
                        ticks: { stepSize: 5, precision: 0 },
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                    },
                },
            },
        });
    }

    /* ── Enrolment weekly trend chart ───────────────────────────── */
    const enrolCtx = document.getElementById("enrolChart");
    if (enrolCtx) {
        const thisWeekE = JSON.parse(enrolCtx.dataset.thisWeek || "[]");
        const lastWeekE = JSON.parse(enrolCtx.dataset.lastWeek || "[]");

        const monday = new Date();
        monday.setDate(monday.getDate() - ((monday.getDay() + 6) % 7));
        monday.setHours(0, 0, 0, 0);

        const fmt = (d) =>
            d.toLocaleDateString("en-AU", { day: "numeric", month: "short" });

        const thisWeekDatesE = Array.from({ length: 7 }, (_, i) => {
            const d = new Date(monday);
            d.setDate(monday.getDate() + i);
            return fmt(d);
        });

        const lastWeekDatesE = Array.from({ length: 7 }, (_, i) => {
            const d = new Date(monday);
            d.setDate(monday.getDate() - 7 + i);
            return fmt(d);
        });

        new Chart(enrolCtx, {
            type: "line",
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                datasets: [
                    {
                        label: "This Week",
                        data: thisWeekE,
                        borderColor: "#2cb67d",
                        backgroundColor: "rgba(44,182,125,.10)",
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#2cb67d",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                    },
                    {
                        label: "Last Week",
                        data: lastWeekE,
                        borderColor: "#86efac",
                        backgroundColor: "rgba(134,239,172,.06)",
                        borderWidth: 2,
                        borderDash: [5, 4],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: "#86efac",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: "index", intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => items[0].label,
                            label: (item) => {
                                const date =
                                    item.datasetIndex === 0
                                        ? thisWeekDatesE[item.dataIndex]
                                        : lastWeekDatesE[item.dataIndex];
                                const v = item.parsed.y;
                                return ` ${item.dataset.label} (${date}): ${v} enrolment${v !== 1 ? "s" : ""}`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: "#f1f3f7", drawBorder: false },
                        ticks: { stepSize: 5, precision: 0 },
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                    },
                },
            },
        });
    }

    /* ── Level bar chart ───────────────────────────────────────── */
    const levelCtx = document.getElementById("levelChart");
    if (levelCtx) {
        const parseArray = (value) => {
            if (!value) return [];
            try {
                return JSON.parse(value);
            } catch (error) {
                return [];
            }
        };

        const labels = parseArray(levelCtx.dataset.labels);
        const visits = parseArray(levelCtx.dataset.visits);
        const enrolments = parseArray(levelCtx.dataset.enrolments);

        new Chart(levelCtx, {
            type: "bar",
            data: {
                labels,
                datasets: [
                    {
                        label: "Visits",
                        data: visits,
                        backgroundColor: "rgba(67,97,238,.80)",
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: "Enrolments",
                        data: enrolments,
                        backgroundColor: "rgba(44,182,125,.80)",
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: "index",
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            borderRadius: 3,
                            useBorderRadius: true,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "#f1f3f7",
                            drawBorder: false,
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                    },
                },
            },
        });
    }

    /* ── Calendar ──────────────────────────────────────────────── */
    let cur = new Date();
    const eventDates = [5, 12, 14, 19, 26];

    function renderCalendar() {
        const grid = document.getElementById("calGrid");
        const title = document.getElementById("calTitle");
        if (!grid || !title) return;

        const y = cur.getFullYear(),
            m = cur.getMonth();
        const today = new Date();
        const monthNames = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];

        title.textContent = `${monthNames[m]} ${y}`;

        const firstDay = new Date(y, m, 1).getDay();
        const totalDays = new Date(y, m + 1, 0).getDate();
        const prevTotal = new Date(y, m, 0).getDate();

        grid.innerHTML = "";

        // Day names row
        ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"].forEach((d) => {
            const dn = document.createElement("div");
            dn.className = "cal-day-name";
            dn.textContent = d;
            grid.appendChild(dn);
        });

        // Leading empty cells (prev month)
        for (let i = 0; i < firstDay; i++) {
            const c = document.createElement("div");
            c.className = "cal-cell is-other";
            c.textContent = prevTotal - firstDay + i + 1;
            grid.appendChild(c);
        }

        // Current month days
        for (let d = 1; d <= totalDays; d++) {
            const c = document.createElement("div");
            c.className = "cal-cell";
            c.textContent = d;

            const isToday =
                d === today.getDate() &&
                m === today.getMonth() &&
                y === today.getFullYear();
            if (isToday) c.classList.add("is-today");

            if (eventDates.includes(d)) {
                const dot = document.createElement("span");
                dot.className = "cal-dot";
                c.appendChild(dot);
            }

            grid.appendChild(c);
        }

        // Trailing cells (next month)
        const total = firstDay + totalDays;
        const trailing = total % 7 === 0 ? 0 : 7 - (total % 7);
        for (let i = 1; i <= trailing; i++) {
            const c = document.createElement("div");
            c.className = "cal-cell is-other";
            c.textContent = i;
            grid.appendChild(c);
        }
    }

    window.prevMonth = function () {
        cur.setMonth(cur.getMonth() - 1);
        renderCalendar();
    };

    window.nextMonth = function () {
        cur.setMonth(cur.getMonth() + 1);
        renderCalendar();
    };

    document.addEventListener("DOMContentLoaded", renderCalendar);
    renderCalendar(); // also call immediately in case DOM is ready
})();
