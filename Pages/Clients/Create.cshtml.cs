using CrmApp.Data;
using CrmApp.Data.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.AspNetCore.Mvc.Rendering;

namespace CrmApp.Pages.Clients
{
    public class CreateModel(ApplicationDbContext context) : PageModel
    {
        public IActionResult OnGet()
        {
            ViewData["UserId"] = new SelectList(context.Users, "Id", "Id");
                return Page();
        }

        [BindProperty]
        public Client Client { get; set; } = null!;

        // For more information, see https://aka.ms/RazorPagesCRUD.
        public async Task<IActionResult> OnPostAsync()
        {
            if (!ModelState.IsValid)
            {
                return Page();
            }

            context.Client.Add(Client);
            await context.SaveChangesAsync();

            return RedirectToPage("./Index");
        }
    }
}
