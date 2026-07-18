using Microsoft.AspNetCore.Identity;

namespace CrmApp.Data.Models;

public class Task
{
    public int Id { get; set; }
    public string Title { get; set; } = string.Empty;
    public string? Description { get; set; }
    public FollowUpStatus Status { get; set; } = FollowUpStatus.Pending;
    public DateTime DueDate { get; set; }
    public bool IsCompleted { get; set; }

    public string? UserId { get; set; }

    public required IdentityUser User { get; set; }
    
    public string? ClientId { get; set; }
    
    public required Client Client { get; set; }

    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
}
