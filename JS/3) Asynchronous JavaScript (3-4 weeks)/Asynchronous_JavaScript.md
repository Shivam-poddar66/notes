# Asynchronous JavaScript (Phase 3)

Last updated: February 25, 2026
Target duration: 3-4 weeks

## Chapter Goal
Master asynchronous behavior in JavaScript across browser and Node runtimes, including event loop internals, async patterns, promises, async/await, and network requests.

## What This Chapter Covers
- Synchronous vs asynchronous execution.
- Web APIs and Node APIs in async architecture.
- Event loop, micro-task queue, macro-task queue.
- Callback patterns and callback hell refactoring.
- Promises, chaining, and error propagation.
- `async`/`await` control flow.
- Promise combinators and flow strategy.
- `fetch` and HTTP request/response lifecycle.
- JSON parsing, validation, retries, cancellation, timeout.
- Production-style async mini-projects.

## Folder Map (Topic-Wise Files)
- `1)_Synchronous_vs_asynchronous_execution.md`
- `2)_Runtime_environment_Web_APIs_and_Node_APIs.md`
- `3)_Event_loop_core_mental_model.md`
- `4)_Macro_task_vs_micro_task_queue_execution_order.md`
- `5)_Callbacks_and_inversion_of_control.md`
- `6)_Callback_hell_and_refactoring_patterns.md`
- `7)_Promises_states_resolution_and_settlement.md`
- `8)_Promise_chaining_and_error_propagation.md`
- `9)_Async_await_control_flow_and_error_handling.md`
- `10)_Promise_all_allSettled_race_any_comparison.md`
- `11)_Sequential_vs_parallel_async_flows.md`
- `12)_Concurrency_limits_and_batch_processing.md`
- `13)_Fetch_API_basics_and_request_options.md`
- `14)_Request_response_lifecycle_end_to_end.md`
- `15)_HTTP_methods_status_codes_headers_and_caching_basics.md`
- `16)_JSON_parsing_validation_and_safe_data_handling.md`
- `17)_Retries_exponential_backoff_and_idempotency_basics.md`
- `18)_AbortController_and_timeout_patterns.md`
- `19)_Mini_project_1_Weather_app_using_public_API.md`
- `20)_Mini_project_2_GitHub_user_finder_with_loading_and_error_states.md`
- `21)_Mini_project_3_Promise_utility_library.md`
- `22)_Async_debugging_checklist_and_common_mistakes.md`
- `23)_Phase_3_study_plan_(3-4_weeks).md`
- `24)_Quick_revision_sheet.md`

## Completion Standard
You complete Phase 3 when you can:
- Explain event loop execution order with correct queue priority.
- Build stable async flows with proper error handling and cancellation.
- Design reliable API calls with retry and timeout strategy.
- Complete all three mini-projects with loading/error states.

## Required Milestone Projects
1. Weather app using public API
2. GitHub user finder with loading/error states
3. Promise utility library

## Study Rule
Predict async behavior -> run code -> trace queues -> handle failure paths -> measure reliability.
