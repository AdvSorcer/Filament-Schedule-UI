# 貢獻指南

感謝您對 Filament Schedule UI 的興趣！我們歡迎所有形式的貢獻。

## 如何貢獻

### 報告問題

如果您發現了 bug 或有功能建議，請在 GitHub 上建立一個 issue。在建立 issue 之前，請先搜尋是否已有相關的 issue。

### 提交 Pull Request

1. **Fork 專案**並克隆到本地
2. **建立分支**：`git checkout -b feature/your-feature-name`
3. **進行修改**並確保代碼符合專案規範
4. **執行測試**：`php artisan test`
5. **格式化代碼**：`vendor/bin/pint`
6. **提交變更**：`git commit -m "Add: your feature description"`
7. **推送分支**：`git push origin feature/your-feature-name`
8. **建立 Pull Request**

## 代碼規範

- 遵循 Laravel 編碼規範
- 使用 Laravel Pint 進行代碼格式化
- 所有新功能必須包含測試
- 確保所有測試通過

## 提交訊息規範

請使用清晰的提交訊息：

- `Add:` 新功能
- `Fix:` 修復 bug
- `Update:` 更新功能
- `Refactor:` 重構代碼
- `Docs:` 文檔更新
- `Style:` 代碼格式調整

## 測試

在提交 PR 之前，請確保：

```bash
# 執行所有測試
php artisan test

# 檢查代碼風格
vendor/bin/pint --test
```

## 問題回報

當回報問題時，請包含：

- Laravel 版本
- PHP 版本
- Filament 版本
- 問題描述
- 重現步驟
- 預期行為
- 實際行為
- 相關截圖（如適用）

## 功能建議

當提出功能建議時，請說明：

- 使用場景
- 預期的行為
- 可能的實現方式（可選）

---

再次感謝您的貢獻！

