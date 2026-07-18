using CrmApp.Data;
using CrmApp.Data.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.EntityFrameworkCore;

namespace CrmApp.Pages.Clients;

public class DeleteModel(ApplicationDbContext context) : PageModel
{
    [BindProperty]
    public Client Client { get; set; } = null!;

    public async Task<IActionResult> OnGetAsync(int? id)
    {
        if (id == null)
        {
            return NotFound();
        }

        var client = await context.Client.FirstOrDefaultAsync(m => m.Id == id);

        if (client is null) return NotFound();
        Client = client;

        return Page();

    }

    public async Task<IActionResult> OnPostAsync(int? id)
    {
        if (id == null)
        {
            return NotFound();
        }

        var client = await context.Client.FindAsync(id);
        if (client == null) return RedirectToPage("./Index");
        
        Client = client;
        context.Client.Remove(Client);
        await context.SaveChangesAsync();

        return RedirectToPage("./Index");
    }
}