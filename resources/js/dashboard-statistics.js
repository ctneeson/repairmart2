document.addEventListener("DOMContentLoaded", function () {
    // Initialize each tab group separately
    initTabGroup("activityTabs", "activityTabsContent");
    initTabGroup("ordersTabGroup", "ordersTabContent");

    // Function to initialize a tab group
    function initTabGroup(tabGroupId, contentGroupId) {
        const tabContainer = document.getElementById(tabGroupId);
        const contentContainer = document.getElementById(contentGroupId);

        if (!tabContainer || !contentContainer) return;

        const tabs = tabContainer.querySelectorAll(".nav-link");
        const tabContents = contentContainer.querySelectorAll(".tab-pane");

        // On initial load, hide any non-active tab content
        tabContents.forEach((content) => {
            if (!content.classList.contains("active")) {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });

        // Add click handlers to each tab
        tabs.forEach((tab) => {
            tab.addEventListener("click", function (e) {
                e.preventDefault();

                // Get the target tab ID from the href attribute or data-bs-target
                let targetId;
                if (
                    this.hasAttribute("href") &&
                    this.getAttribute("href").startsWith("#")
                ) {
                    targetId = this.getAttribute("href").substring(1);
                } else if (this.hasAttribute("data-bs-target")) {
                    targetId = this.getAttribute("data-bs-target").substring(1);
                }

                if (targetId) {
                    // Only apply the tab switch within this tab group
                    showTabInGroup(targetId, tabs, tabContents);
                }
            });
        });
    }

    // Function to show a tab within a specific tab group
    function showTabInGroup(tabId, groupTabs, groupContents) {
        // Hide all tab contents in this group
        groupContents.forEach((content) => {
            content.style.display = "none";
            content.classList.remove("show", "active");
        });

        // Remove active class from all tabs in this group
        groupTabs.forEach((tab) => {
            tab.classList.remove("active");
            tab.setAttribute("aria-selected", "false");
        });

        // Show the selected tab content
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.style.display = "block";
            selectedTab.classList.add("show", "active");

            // Add active class to the selected tab
            let activeTabLink;
            if (selectedTab.getAttribute("aria-labelledby")) {
                // If the tab content has an aria-labelledby attribute
                activeTabLink = document.getElementById(
                    selectedTab.getAttribute("aria-labelledby")
                );
            } else {
                // Try to find by href or data-bs-target
                activeTabLink =
                    document.querySelector(`.nav-link[href="#${tabId}"]`) ||
                    document.querySelector(
                        `.nav-link[data-bs-target="#${tabId}"]`
                    );
            }

            if (activeTabLink) {
                activeTabLink.classList.add("active");
                activeTabLink.setAttribute("aria-selected", "true");
            }
        }
    }

    // Handle period changes for the activity widgets
    const periodSelect = document.getElementById("activity_period");
    if (periodSelect) {
        periodSelect.addEventListener("change", function () {
            updateCounts(this.value);
        });
    }

    function updateCounts(days) {
        const countElements = document.querySelectorAll("[data-counts]");
        countElements.forEach((element) => {
            const countsData = JSON.parse(element.getAttribute("data-counts"));
            if (countsData && countsData[days] !== undefined) {
                element.textContent = countsData[days];
            } else {
                element.textContent = "0";
            }
        });
    }
});
