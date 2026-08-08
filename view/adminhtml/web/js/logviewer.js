define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, uiRegistry, confirm, $t) {
    'use strict';

    return function (config) {
        var viewUrl = config.viewUrl;
        var downloadUrl = config.downloadUrl;
        var deleteUrl = config.deleteUrl;

        var logFileSelect = $('#log_file_select');
        var linesToDisplay = $('#lines_to_display');
        var viewLogButton = $('#view_log_button');
        var downloadLogButton = $('#download_log_button');
        var deleteLogButton = $('#delete_log_button');
        var logContent = $('#log_content');
        var fileSizeSpan = $('#file_size');
        var lastModifiedSpan = $('#last_modified');

        function loadLogContent() {
            var fileName = logFileSelect.val();
            var lines = linesToDisplay.val();

            if (!fileName) {
                alert($t('Please select a log file.'));
                return;
            }

            if (lines <= 0) {
                alert($t('Number of lines must be a positive integer.'));
                return;
            }

            logContent.text($t('Loading...'));
            fileSizeSpan.text('N/A');
            lastModifiedSpan.text('N/A');

            $.ajax({
                url: viewUrl,
                data: {
                    file: fileName,
                    lines: lines
                },
                type: 'GET',
                dataType: 'json',
                showLoader: true,
                success: function (response) {
                    if (response.error) {
                        alert(response.message);
                        logContent.text('');
                    } else {
                        logContent.text(response.content);
                        fileSizeSpan.text(formatBytes(response.file_size));
                        lastModifiedSpan.text(response.last_modified);
                    }
                },
                error: function (xhr, status, error) {
                    alert($t('An error occurred while loading the log file.'));
                    logContent.text('');
                }
            });
        }

        function downloadLogFile() {
            var fileName = logFileSelect.val();
            if (!fileName) {
                alert($t('Please select a log file to download.'));
                return;
            }
            window.location.href = downloadUrl + '?file=' + fileName;
        }

        function deleteLogFile() {
            var fileName = logFileSelect.val();
            if (!fileName) {
                alert($t('Please select a log file to delete.'));
                return;
            }

            confirm({
                content: $t('Are you sure you want to delete the log file?'),
                actions: {
                    confirm: function () {
                        $.ajax({
                            url: deleteUrl,
                            data: {
                                file: fileName
                            },
                            type: 'POST',
                            dataType: 'json',
                            showLoader: true,
                            success: function (response) {
                                if (response.error) {
                                    alert(response.message);
                                } else {
                                    alert(response.message);
                                    // Reload the page or update the file list
                                    location.reload(); 
                                }
                            },
                            error: function (xhr, status, error) {
                                alert($t('An error occurred while deleting the log file.'));
                            }
                        });
                    }
                }
            });
        }

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Event Listeners
        viewLogButton.on('click', loadLogContent);
        downloadLogButton.on('click', downloadLogFile);
        deleteLogButton.on('click', deleteLogFile);
        logFileSelect.on('change', loadLogContent); // Load content when file selection changes

        // Initial load if a file is pre-selected (e.g., after a refresh)
        if (logFileSelect.val()) {
            loadLogContent();
        }
    };
});
