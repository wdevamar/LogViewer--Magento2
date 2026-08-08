# Magento 2 Log Viewer Extension

This extension provides a convenient way for Magento 2 administrators to view, download, and delete log files directly from the backend, without requiring server access.

## Features

*   **Admin Menu Integration:** Accessible via `System > Log Viewer`.
*   **Log File Selection:** Automatically scans `var/log/` and displays available log files with their size and last modified date.
*   **Log Preview:** View the last N lines of any selected log file (default 100 lines).
*   **Full Log Download:** Download the complete log file.
*   **Log File Cleanup:** Delete individual log files with confirmation.
*   **Security:** ACL-based permissions for viewing, downloading, and deleting logs. Prevents directory traversal.
*   **User Interface:** Utilizes Magento 2 UI components for a responsive and AJAX-driven experience.

## Installation

1.  **Manual Installation:**

    a.  Create the directory `app/code/WdevAmar/LogViewer` in your Magento root.
    b.  Copy all the contents of this extension into the newly created directory.

    ```bash
    # Example using rsync from your extension source directory
    rsync -av /path/to/WdevAmar_LogViewer/ app/code/WdevAmar/LogViewer/
    ```

2.  **Enable the Module:**

    Run the following commands from your Magento root directory:

    ```bash
    php bin/magento module:enable WdevAmar_LogViewer
    php bin/magento setup:upgrade
    php bin/magento cache:clean
    php bin/magento cache:flush
    ```

## Configuration

After installation, navigate to `System > Permissions > User Roles` in your Magento Admin Panel. Edit the relevant user roles and grant permissions for `WdevAmar_LogViewer` under `System > Log Viewer`.

*   **View Logs:** Allows users to access the Log Viewer and preview log content.
*   **Download Logs:** Allows users to download full log files.
*   **Delete Logs:** Allows users to delete log files.

## Usage

1.  Log in to your Magento Admin Panel.
2.  Navigate to `System > Log Viewer`.
3.  On the Log Viewer page:
    *   Select a log file from the dropdown menu.
    *   Enter the number of lines you wish to preview (default is 100).
    *   Click **View Log/Refresh** to display the content.
    *   Click **Download Full File** to download the selected log file.
    *   Click **Erase Log File** to delete the selected log file (a confirmation dialog will appear).


